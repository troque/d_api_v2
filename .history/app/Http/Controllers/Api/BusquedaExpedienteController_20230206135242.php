<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusquedaExpedienteFormRequest;
use App\Http\Resources\BusquedaExpediente\BusquedaExpedienteCollection;
use App\Http\Resources\BusquedaExpediente\BusquedaExpedienteResource;
use App\Models\BusquedaExpedienteModel;
use App\Models\TipoLogModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class BusquedaExpedienteController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new BusquedaExpedienteModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = BusquedaExpedienteModel::query();
        $query = $query->select('mas_busqueda_expediente.id, mas_busqueda_expediente.nombre, mas_busqueda_expediente.estado')->orderBy('mas_busqueda_expediente.nombre', 'asc')->get();

        return BusquedaExpedienteCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\BusquedaExpedienteFormRequest  $request
     * @return \Illuminate\Http\BusquedaExpedienteResource
     */
    public function store(BusquedaExpedienteFormRequest $request)
    {
        return BusquedaExpedienteResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return BusquedaExpedienteResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BusquedaExpedienteFormRequest $request, $id)
    {
        return BusquedaExpedienteResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
