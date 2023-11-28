<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParametrizacionActuacionesModel;
use App\Http\Requests\ParametrizacionActuacionesFormRequest;
use App\Http\Resources\ParametrizacionActuaciones\ParametrizacionActuacionesCollection;
use App\Http\Resources\ParametrizacionActuaciones\ParametrizacionActuacionesResource;
use Illuminate\Support\Facades\DB;
use App\Repositories\RepositoryGeneric;

class ParametrizacionActuacionesController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ParametrizacionActuacionesModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ParametrizacionActuacionesFormRequest $request)
    {
        try {

            // Se inicializa la conexion
            DB::connection()->beginTransaction();

            // Se capturan los datos
            $datosRequest = $request->validated();

            // Se captura la fecha
            $año = date("Y");
            $mes = date("m");
            $dia = date("d");
            $hor = date("h");
            $min = date("i");
            $sec = date("s");
            $masActuacionesNombreCarpeta = "mas_actuaciones";
            $rutaSinStoragePath = '/files' . '/' . $masActuacionesNombreCarpeta . '/' . $año . '/' . $mes . '/' . $dia . '/' . $año . $mes . $dia . $hor . $min . $sec . '_' . $datosRequest['nombre_plantilla'];
            $path = storage_path() . '/files' . '/' . $masActuacionesNombreCarpeta . '/' . $año . '/' . $mes . '/' . $dia;
            $dest = storage_path() . '/files' . '/' . $masActuacionesNombreCarpeta . '/' . $año . '/' . $mes . '/' . $dia . '/' . $hor . $min . $sec . $datosRequest['nombre_plantilla'];

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Campos de la tabla
            $datosRequest['nombre_actuacion'] = substr($datosRequest['nombre_actuacion'], 0, 254);
            $datosRequest['nombre_plantilla'] = $rutaSinStoragePath;
            $datosRequest['id_etapa'] = $datosRequest['id_etapa'];
            $datosRequest['estado'] = $datosRequest['estado'];
            $datosRequest['id_etapa_despues_aprobacion'] = $datosRequest['id_etapa_despues_aprobacion'];
            $datosRequest['despues_aprobacion_listar_actuacion'] = $datosRequest['despues_aprobacion_listar_actuacion'];

            // Se manda el array del modelo con su informacion para crearlo en su tabla
            $respuesta = ParametrizacionActuacionesResource::make($this->repository->create($datosRequest));
            $array = json_decode(json_encode($respuesta));

            // Se guarda la ejecucion con un commit para que se ejecute
            DB::connection()->commit();

            // Se retorna la respuesta
            return $respuesta;
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Método que todas las parametrizaciones de actuaciones del sistema creadas y ordenadas por fecha de creacion
     *
     */
    public function getAllParametrizacionActuaciones(Request $request)
    {

        $results = DB::select("
        SELECT ma.id, ma.nombre_actuacion, ma.id_etapa, ma.nombre_plantilla, me.nombre, me.estado, ma.id_etapa_despues_aprobacion,
        (select me.nombre from mas_etapa me where me.id = ma.id_etapa_despues_aprobacion) as etapa_despues_aprobacion
        FROM mas_actuaciones ma
        INNER JOIN mas_etapa me on me.id = ma.id_etapa
        INNER JOIN mas_etapa me on me.id = ma.id_etapa_despues_aprobacion
        ORDER BY ma.created_at asc");
        return  json_encode($results);
    }
}