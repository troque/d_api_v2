<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogEtapaFormRequest;
use App\Http\Resources\LogEtapa\LogEtapaCollection;
use App\Http\Resources\LogEtapa\LogEtapaResource;
use App\Models\LogEtapaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class LogEtapaController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository) {
        $this->repository = $repository;
        $this->repository->setModel(new LogEtapaModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return LogEtapaCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LogEtapaFormRequest $request)
    {
        try {

            $datosRequest = $request->validated();
            return LogEtapaResource::make($this->repository->create($datosRequest));


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
        return LogEtapaResource::make($this->repository->find($id));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

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


    public function getLogEtapaByIdProcesoDisciplinario($procesoDiciplinarioUUID, LogEtapaFormRequest $request)
    {
        $datosRequest = $request->validated();

        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID, $datosRequest) {
            return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)
                ->where('estado', $datosRequest['estado'])
                ->orderBy('antecedente.created_at', 'desc')
                ->get();
        });

        return AntecedenteCollection::make($query);
    }
}
