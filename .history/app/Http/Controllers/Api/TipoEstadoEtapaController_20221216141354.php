<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoDocumentoFormRequest;
use App\Http\Requests\TipoEstadoEtapaFormRequest;
use App\Models\TipoEstadoEtapaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TipoEstadoEtapaController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoEstadoEtapaModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\TipoDocumentoFormRequest  $request
     * @return App\Http\Resources\TipoDocumento\TipoDocumentoResource
     */
    public function store(TipoEstadoEtapaFormRequest $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoDocumentoFormRequest $request,  $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
