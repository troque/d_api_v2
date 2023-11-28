<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntidadFuncionarioQuejaInternaFormRequest;
use App\Http\Resources\EntidadFuncionarioQuejaInterna\EntidadFuncionarioQuejaInternaCollection;
use App\Http\Resources\EntidadFuncionarioQuejaInterna\EntidadFuncionarioQuejaInternaResource;
use App\Models\EntidadFuncionarioQuejaInternaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class EntidadFuncionarioQuejaInternaController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new EntidadFuncionarioQuejaInternaModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return  EntidadFuncionarioQuejaInternaCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EntidadFuncionarioQuejaInternaFormRequest $request)
    {
        // Se captura la informacion
        $datosRequest = $request->validated()["data"]["attributes"];

        // Se retorna la informacion
        return EntidadFuncionarioQuejaInternaResource::make($this->repository->create($datosRequest));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return EntidadFuncionarioQuejaInternaResource::make($this->repository->find($id));
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
     *
     */
    public function getEntidadesQuejaInternaByProcesoDisciplinario($id_proceso_disciplinario)
    {

        $query = $this->repository->customQuery(function ($model) use ($id_proceso_disciplinario) {
            return $model->where('id_proceso_disciplinario', $id_proceso_disciplinario)->get();
        });

        return EntidadFuncionarioQuejaInternaCollection::make($query);
    }
}
