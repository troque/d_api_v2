<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortalConfiguracionTipoInteresadoFormRequest;
use App\Http\Resources\PortalConfiguracionTipoInteresado\PortalConfiguracionTipoInteresadoCollection;
use App\Http\Resources\PortalConfiguracionTipoInteresado\PortalConfiguracionTipoInteresadoResource;
use Illuminate\Http\Request;
use App\Repositories\RepositoryGeneric;
use App\Models\PortalConfiguracionTipoInteresadoModel;

class PortalConfiguracionTipoInteresadoController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new PortalConfiguracionTipoInteresadoModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return PortalConfiguracionTipoInteresadoCollection::make($this->repository->paginate($request->limit ?? 500));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PortalConfiguracionTipoInteresadoFormRequest $request)
    {
        return PortalConfiguracionTipoInteresadoResource::make($this->repository->create($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return PortalConfiguracionTipoInteresadoResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PortalConfiguracionTipoInteresadoFormRequest $request, $id)
    {
        return PortalConfiguracionTipoInteresadoResource::make($this->repository->update($request->validated(), $id));
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