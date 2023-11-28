<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TempClasificacionRadicadoFormRequest;
use App\Http\Resources\TempClasificacionRadicado\TempClasificacionRadicadoCollection;
use App\Http\Resources\TempProcesoDisciplinario\TempProcesoDisciplinarioResource;
use App\Models\TempClasificacionRadicadoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TempClasificacionRadicadoController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TempClasificacionRadicadoModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TempClasificacionRadicadoCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TempClasificacionRadicadoFormRequest $request)
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
        return TempProcesoDisciplinarioResource::make($this->repository->find($id));
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
}
