<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EstadoActuacionesModel;
use App\Http\Requests\EstadoActuacionesFormRequest;
use App\Http\Resources\EstadoActuaciones\EstadoActuacionesCollection;
use App\Http\Resources\EstadoActuaciones\EstadoActuacionesResource;
use Illuminate\Support\Facades\DB;
use App\Repositories\RepositoryGeneric;

class EstadoActuacionesController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new EstadoActuacionesModel());
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
    public function store(EstadoActuacionesFormRequest $request)
    {
        try {

            // Se inicializa la conexion
            DB::connection()->beginTransaction();

            // Se capturan los datos
            $datosRequest = $request->validated()["data"]["attributes"];

            // Campos de la tabla
            $datosRequest['nombre'] = substr($datosRequest['nombre'], 0, 254);
            $datosRequest['codigo'] = substr($datosRequest['codigo'], 0, 254);
            $datosRequest['descripcion'] = substr($datosRequest['descripcion'], 0, 254);

            // Se manda el array del modelo con su informacion para crearlo en su tabla
            $respuesta = EstadoActuacionesResource::make($this->repository->create($datosRequest));
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
     * Método que todas las actuaciones del sistema creadas y ordenadas por fecha de creacion
     *
     */
    public function getAllEstadoActuaciones(Request $request)
    {
        $results = DB::select("select * from mas_estado_actuaciones order by created_at asc");
        return  json_encode($results);
    }
}
