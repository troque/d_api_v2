<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActuacionPorSemaforoFormRequest;
use App\Http\Resources\ActuacionPorSemaforo\ActuacionPorSemaforoCollection;
use App\Http\Resources\ActuacionPorSemaforo\ActuacionPorSemaforoResource;
use App\Models\ActuacionPorSemaforoModel;
use App\Models\DiasNoLaboralesModel;
use App\Repositories\RepositoryGeneric;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActuacionPorSemaforoController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ActuacionPorSemaforoModel());
    }

    /**
     *
     */
    public function index(Request $request)
    {
        return ActuacionPorSemaforoCollection::make($this->repository->paginate($request->limit ?? 100));
    }

    /**
     *
     */
    public function store(ActuacionPorSemaforoFormRequest $request)
    {
        try {
            return ActuacionPorSemaforoFormRequest::make($this->repository->create($request->validated()["data"]["attributes"]));
        } catch (\Exception $e) {

            if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                $error['estado'] = false;
                $error['error'] = 'Ya existe un registro con ese nombre.';

                return json_encode($error);
            }
        }
    }

    public function show($id)
    {
        return ActuacionPorSemaforoResource::make($this->repository->find($id));
    }

    /**
     *
     */
    public function update(ActuacionPorSemaforoFormRequest $request, $id)
    {
        return ActuacionPorSemaforoResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    public function existeSemaforoConFecha($id_semaforo)
    {
        $query = $this->repository->customQuery(function ($model) use ($id_semaforo) {
            return $model->where('id_semaforo', $id_semaforo)
                ->where('estado', '1')
                ->get();
        });
        return ActuacionPorSemaforoCollection::make($query);
    }

    /**
     *
     */
    public function getSemaforosPorProceso($uuid)
    {
        $query = $this->repository->customQuery(function ($model) use ($uuid) {
            return $model->where('id_proceso_disciplinario', $uuid)
                ->where('estado', '1')
                ->get();
        });
        return ActuacionPorSemaforoCollection::make($query);
    }

    public function diasNoLaborales($inicio, $fin)
    {
        $Laborales = DiasNoLaboralesModel::where("estado", 1)->orderBy("fecha", 'desc')->get();
        $dias2 = 0;
        $fecha_inicio = strtotime($inicio);
        $fecha_fin = strtotime($fin);
        foreach ($Laborales as $key2 => $value2) {

            $fecha = date_create($value2->fecha);
            $fechaaa = strtotime(date_format($fecha, "Y-m-d"));
            if (($fechaaa >= $fecha_inicio) && ($fechaaa <= $fecha_fin)) {
                $dias2++;
            }
        }

        return $dias2;
    }

    public function getDiasTranscurridos($uuid)
    {
        // Declaro variables a usar como el array que retornare y la fechas
        $arr = array();
        $date = Carbon::now();

        $query = $this->repository->customQuery(function ($model) use ($uuid) {
            return $model->where('id_actuacion', $uuid)
                ->where('estado', '1')
                ->whereNull('eliminado')
                ->orWhere('eliminado', '0')
                ->get();
        });


        // Realizo un ciclo para recorrer todos los semaforos que obtuve en la consulta anterior
        foreach ($query as $key => $value) {

            //creamos las fechas y les damos formato
            $datetime1 = date_create($value->fecha_inicio);
            $datetimeee = date_format($datetime1, "Y-m-d");

            $datetime2 = date_create($value->fecha_fin);
            $datetimeee2 = date_format($datetime2, "Y-m-d");

            $diasHastaFinalizar = null;
            $diasCalendarioHastaFinalizar = null;

            error_log($value->fechafinalizo);

            if ($value->fechafinalizo != null) {
                $datetime3 = date_create($value->fechafinalizo);
                $datetimeee3 = date_format($datetime3, "Y-m-d");
                $diasCalendarioHastaFinalizar = date_diff($datetime2, $datetime3)->format('%a');
                $diasNoLaboralesHastaFinalizar = $this->diasNoLaborales($datetimeee2, $datetimeee3);
                $diasHastaFinalizar = intval($diasCalendarioHastaFinalizar) - $diasNoLaboralesHastaFinalizar;
            }


            // obtengo la diferencia en dias de las fechas
            $diasCalendario = date_diff($datetime2, $datetime1)->format('%a');



            // usamos una funcion que nos retorna la cantidad de dias no laborales entre las fechas
            $diasNoLaborales = $this->diasNoLaborales($datetimeee, $datetimeee2);


            // luego le restamos a la cantidad de dias entre las fechas el numero de dias no laborales
            $diasLaborales = intval($diasCalendario) - $diasNoLaborales;
            error_log(intval($diasCalendario));

            // Consulto el registro de la maestra de semaforo usando el id
            $results = DB::select(DB::raw("select id_mas_evento_inicio from semaforo where id = " . $value->id_semaforo));

            foreach ($results as $key => $value2) {
                // error_log($value2->id_mas_actuacion_inicia);
                if ($value2->id_mas_evento_inicio == 6) {
                    $evento = DB::select(DB::raw(
                        "
                    select nombre from mas_evento_inicio
                    where id =" . $value2->id_mas_evento_inicio
                    ));
                }

                foreach ($evento as $key => $even) {
                    array_push(
                        $arr,
                        array(
                            "type" => "diasTranscurridos",
                            "attributes" => array(
                                "actuacionxsemaforo" => ActuacionPorSemaforoResource::make($value),
                                "nombreEventoInicio" => $even->nombre,
                                "nombreMasActuacion" => isset($even->nombre_actuacion) ? $even->nombre_actuacion : null,
                                "diasTranscurridos" => $diasCalendario,
                                "diasTranscurridos2" =>  strval($diasLaborales),
                                "diasTranscurridosHastaFinalizar" => $diasHastaFinalizar,
                                "diasTranscurridosHastaFinalizar2" => $diasCalendarioHastaFinalizar,
                            )
                        )
                    );
                }
            }
        }

        $rtaFinal = array(
            "data" => $arr
        );


        return json_encode($rtaFinal);
    }

    public function FinalizarSemaforo($id_semaforo, $id_proceso_disciplinario)
    {
        try {
            $date = Carbon::now();
            $date->format('Y-m-d');
            ActuacionPorSemaforoModel::where('id_semaforo', $id_semaforo)->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['finalizo' => "si", 'fechafinalizo' => $date->toDateString()]);
            DB::connection()->commit();
            return "exito";
        } catch (\Exception $e) {

            if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                $error['estado'] = false;
                $error['error'] = 'Ya existe un registro con ese nombre.';

                return json_encode($error);
            }
        }
    }
}
