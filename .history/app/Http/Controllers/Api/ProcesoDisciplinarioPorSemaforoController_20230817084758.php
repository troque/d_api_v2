<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcesoDisciplinarioPorSemaforoFormRequest;
use App\Http\Requests\SemeforoProcesoDisciplinarioFormRequest;
use App\Http\Resources\ProcesoDisciplinarioPorSemaforo\ProcesoDisciplinarioPorSemaforoCollection;
use App\Http\Resources\ProcesoDisciplinarioPorSemaforo\ProcesoDisciplinarioPorSemaforoResource;
use App\Models\ActuacionPorSemaforoModel;
use App\Models\DiasNoLaboralesModel;
use App\Models\ProcesoDisciplinarioPorSemaforoModel;
use App\Repositories\RepositoryGeneric;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcesoDisciplinarioPorSemaforoController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ProcesoDisciplinarioPorSemaforoModel());
    }

    /**
     *
     */
    public function index(Request $request)
    {
        $query = $this->repository->customQuery(function ($model) {
            return $model->whereNull('eliminado')
                ->orWhere('eliminado', '0')
                ->orderBy('created_at', 'desc')
                ->get();
        });
        return ProcesoDisciplinarioPorSemaforoCollection::make($query);
    }

    /**
     *
     */
    public function store(ProcesoDisciplinarioPorSemaforoFormRequest $request)
    {
        try {
            return ProcesoDisciplinarioPorSemaforoFormRequest::make($this->repository->create($request->validated()["data"]["attributes"]));
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
        return ProcesoDisciplinarioPorSemaforoResource::make($this->repository->find($id));
    }

    /**
     *
     */
    public function update(ProcesoDisciplinarioPorSemaforoFormRequest $request, $id)
    {
        return ProcesoDisciplinarioPorSemaforoResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    public function existeSemaforoConFecha($id_semaforo)
    {
        $query = $this->repository->customQuery(function ($model) use ($id_semaforo) {
            return $model->where('id_semaforo', $id_semaforo)
                ->where('estado', '1')
                ->get();
        });
        return ProcesoDisciplinarioPorSemaforoCollection::make($query);
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
        return ProcesoDisciplinarioPorSemaforoCollection::make($query);
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

        // Realizo consulta donde se filtra el semaforo por proceso disciplinario y que esta en estado activo
        $query = $this->repository->customQuery(function ($model) use ($uuid) {
            return $model->where('id_proceso_disciplinario', $uuid)
                ->where('estado', '1')
                ->orderBy('created_at', 'desc')
                ->get();
        });

        // Realizo un ciclo para recorrer todos los semaforos que obtuve en la consulta anterior
        foreach ($query as $key => $value) {
            //creamos las fechas y les damos formato
            $datetime1 = date_create($date);
            $datetimeee = date_format($datetime1, "m-d-Y");

            $datetime2 = date_create($value->fecha_inicio);
            $datetimeee2 = date_format($datetime2, "m-d-Y");

            $datetime3 = date_create($value->fechafinalizo);
            $datetimeee3 = date_format($datetime3, "m-d-Y");

            // obtengo la diferencia en dias de las fechas
            $diasCalendario = date_diff($datetime2, $datetime1)->format('%R%a');
            $diasCalendarioHastaFinalizar = date_diff($datetime2, $datetime3)->format('%R%a');

            // usamos una funcion que nos retorna la cantidad de dias no laborales entre las fechas
            $diasNoLaborales = $this->diasNoLaborales($datetimeee2, $datetimeee);
            $diasNoLaboralesHastaFinalizar = $this->diasNoLaborales($datetimeee2, $datetimeee3);

            // luego le restamos a la cantidad de dias entre las fechas el numero de dias no laborales
            $diasLaborales = intval($diasCalendario) - $diasNoLaborales;
            $diasHastaFinalizar = intval($diasCalendarioHastaFinalizar) - $diasNoLaboralesHastaFinalizar;

            // Consulto el registro de la maestra de semaforo usando el id
            $results = DB::select(DB::raw("select id_mas_actuacion_inicia, id_mas_evento_inicio from semaforo where id = " . $value->id_semaforo));


            foreach ($results as $key => $value2) {
                if ($value2->id_mas_actuacion_inicia != null) {
                    $evento = DB::select(DB::raw(
                        "
                    select mei.nombre, ma.nombre_actuacion from mas_evento_inicio mei
                    INNER JOIN mas_actuaciones ma ON ma.id =" . $value2->id_mas_actuacion_inicia . "
                    where mei.id =" . $value2->id_mas_evento_inicio
                    ));
                } else {
                    $evento = DB::select(DB::raw(
                        "
                    select nombre from mas_evento_inicio
                    where id =" . $value2->id_mas_evento_inicio
                    ));
                }

                foreach ($evento as $key => $even) {
                    //error_log($even);
                    array_push(
                        $arr,
                        array(
                            "type" => "diasTranscurridos",
                            "attributes" => array(
                                "pdxsemaforo" => ProcesoDisciplinarioPorSemaforoResource::make($value),
                                "nombreEventoInicio" => $even->nombre,
                                "nombreMasActuacion" => isset($even->nombre_actuacion) ? $even->nombre_actuacion : null,
                                "diasTranscurridos" => $diasCalendario,
                                "diasTranscurridos2" => $diasLaborales,
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

    public function FinalizarSemaforo(SemeforoProcesoDisciplinarioFormRequest $request)
    {
        try {

            $datosRequest = $request->validated()["data"]["attributes"];

            DB::connection()->beginTransaction();
            $date = Carbon::now();
            $date->format('Y-m-d');

            ProcesoDisciplinarioPorSemaforoModel::where('id_semaforo', $datosRequest['id_semaforo'])
                ->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])
                ->whereNull('finalizo')
                ->update(['finalizo' => "si", 'fechafinalizo' => $date->toDateString()]);

            if ($datosRequest['id_actuacion_finaliza']) {
                ProcesoDisciplinarioPorSemaforoModel::where('id_semaforo', $datosRequest['id_semaforo'])
                    ->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])
                    ->whereNull('id_actuacion_finaliza')
                    ->update(['id_actuacion_finaliza' => $datosRequest['id_actuacion_finaliza']]);
            }
            if ($datosRequest['id_dependencia_finaliza']) {
                ProcesoDisciplinarioPorSemaforoModel::where('id_semaforo', $datosRequest['id_semaforo'])
                    ->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])
                    ->whereNull('id_dependencia_finaliza')
                    ->update(['id_dependencia_finaliza' => $datosRequest['id_dependencia_finaliza']]);
            }
            if ($datosRequest['id_usuario_finaliza']) {
                ProcesoDisciplinarioPorSemaforoModel::where('id_semaforo', $datosRequest['id_semaforo'])
                    ->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])
                    ->whereNull('id_usuario_finaliza')
                    ->update(['id_usuario_finaliza' => $datosRequest['id_usuario_finaliza']]);
            }

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

    public function cambiarFechaInicio($id_semaforo, $id_proceso_disciplinario, $fecha)
    {
        try {
            ProcesoDisciplinarioPorSemaforoModel::where('id_semaforo', $id_semaforo)->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['fecha_inicio' => $fecha]);
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
