<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PortalDocumentoNotificacionesFormRequest;
use App\Http\Resources\PortalDocumentoNotificaciones\PortalDocumentoNotificacionesCollection;
use App\Http\Resources\PortalDocumentoNotificaciones\PortalDocumentoNotificacionesResource;
use App\Repositories\RepositoryGeneric;
use App\Models\PortalDocumentoNotificacionesModel;

class PortalDocumentoNotificacionesController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new PortalDocumentoNotificacionesModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Se retorna los valores
        return PortalDocumentoNotificacionesCollection::make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PortalDocumentoNotificacionesFormRequest $request)
    {
        return PortalDocumentoNotificacionesResource::make($this->repository->create($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return PortalDocumentoNotificacionesResource::make($this->repository->find($id));
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
        $this->repository->delete($id);
        return response()->noContent();
    }
}