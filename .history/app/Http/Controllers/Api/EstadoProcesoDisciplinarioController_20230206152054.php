<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EstadoProcesoDisciplinarioFormRequest;
use App\Http\Resources\EstadoProcesoDisciplinario\EstadoProcesoDisciplinarioCollection;
use App\Http\Resources\EstadoProcesoDisciplinario\EstadoProcesoDisciplinarioResource;
use App\Models\EstadoProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class EstadoProcesoDisciplinarioController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new EstadoProcesoDisciplinarioModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = EstadoProcesoDisciplinarioModel::query();
        $query = $query->select(
            'mas_estado_proceso_disciplinario.id',
            'mas_estado_proceso_disciplinario.nombre',
            'mas_estado_proceso_disciplinario.estado'
        )->orderBy('mas_estado_proceso_disciplinario.nombre', 'asc')->get();

        return EstadoProcesoDisciplinarioCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\EstadoProcesoDisciplinarioFormRequest  $request
     * @return \Illuminate\Http\Resources\EstadoProcesoDisciplinarioResource
     */
    public function store(EstadoProcesoDisciplinarioFormRequest $request)
    {
        return EstadoProcesoDisciplinarioResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return EstadoProcesoDisciplinarioResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EstadoProcesoDisciplinarioFormRequest $request, $id)
    {
        return EstadoProcesoDisciplinarioResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->noContent();
    }
}
