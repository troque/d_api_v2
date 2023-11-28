<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Requests\TipoConductaProcesoDisciplinarioFormRequest;
use App\Http\Resources\TipoConductaProcesoDisciplinario\TipoConductaProcesoDisciplinarioCollection;
use App\Http\Resources\TipoConductaProcesoDisciplinario\TipoConductaProcesoDisciplinarioResource;
use App\Models\TipoConductaProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TipoConductaProcesoDisciplinarioController extends Controller

{
    private $repository;
    use LogTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoConductaProcesoDisciplinarioModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TipoConductaProcesoDisciplinarioCollection::make($this->repository);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\TipoConductaFormRequest  $request
     * @return \Illuminate\Http\Resources\TipoConductaResource
     */
    public function store(TipoConductaProcesoDisciplinarioFormRequest $request): TipoConductaProcesoDisciplinarioResource
    {

        $datosRequest = $request->validated()["data"]["attributes"];
        $datosRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
        $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;

        return TipoConductaProcesoDisciplinarioResource::make($this->repository->create($datosRequest));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TipoConductaProcesoDisciplinarioResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoConductaProcesoDisciplinarioFormRequest $request, $id)
    {
        return TipoConductaProcesoDisciplinarioResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }


    /**
     *
     */
    public function showTipoConductaByProcesoDisciplinario($id_proceso_disciplinario)
    {

        $query = $this->repository->customQuery(function ($model) use ($id_proceso_disciplinario) {
            return $model->where('id_proceso_disciplinario', $id_proceso_disciplinario)->get();
        });

        return TipoConductaProcesoDisciplinarioCollection::make($query);
    }
}
