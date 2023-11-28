<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogAntecedenteFormRequest;
use App\Http\Resources\LogAntecedente\LogAntecedenteCollection;
use App\Http\Resources\LogAntecedente\LogAntecedenteResource;
use App\Models\LogAntecedenteModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class LogAntecedenteController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository) {
        $this->repository = $repository;
        $this->repository->setModel(new LogAntecedenteModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return LogAntecedenteCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LogAntecedenteFormRequest $request)
    {
        try {

            $datosRequest = $request->validated();
            /*$datosRequest['fecha_registro'] = date('Y-m-d H:m:s');
            $datosRequest['estado'] = 1;
            $datosRequest['id_etapa'] = 1;
            $datosRequest['descripcion'] = substr($datosRequest['descripcion'], 0, 4000);*/
            return LogAntecedenteResource::make($this->repository->create($datosRequest));


        } catch (QueryException  $e) {

            $error['estado'] = false;
            $error['error'] = $e->getMessage();
            return json_encode($error);
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
        return LogAntecedenteResource::make($this->repository->find($id));
    }


   /**
    *
    */
    public function getAllAntecedentesByIdProcesoDisciplinario($procesoDiciplinarioUUID, AntecedenteFormRequest $request){

        $datosRequest = $request->validated();

        /*$query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID, $datosRequest)
        {
            return $model
            ->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)
            ->where('estado', '=', $datosRequest['estado'])
            ->leftJoin('mas_dependencia_origen', 'id_dependencia', '=', 'mas_dependencia_origen.id')
            ->leftJoin('mas_etapa', 'id_etapa', '=', 'mas_etapa.id')
            ->select('antecedente.*', 'mas_dependencia_origen.nombre AS nombre_dependencia', 'mas_etapa.nombre as nombre_etapa')
            ->orderBy('antecedente.created_at', 'desc')->get();
        });

        return AntecedenteCollection::make($query);*/


        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID, $datosRequest)
        {
            return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)
            ->where('estado', $datosRequest['estado'])
            ->orderBy('antecedente.created_at', 'desc')
            ->get();
        });


        return AntecedenteCollection::make($query);

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
        $datosRequest = $request->only('estado');
        $query = AntecedenteResource::make($this->repository->find($id));

        if($query['estado'] == true){
            $datosRequest['estado'] = false;
            return AntecedenteResource::make($this->repository->update($datosRequest, $id));
        }
        else{
            $datosRequest['estado'] = true;
            return AntecedenteResource::make($this->repository->update($datosRequest, $id));
        }
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
}
