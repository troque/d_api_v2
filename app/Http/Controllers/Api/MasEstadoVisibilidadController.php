<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MasEstadoVisibilidad\MasEstadoVisibilidadCollection;
use App\Http\Utilidades\Constants;
use App\Models\MasEstadoVisibilidadModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class MasEstadoVisibilidadController extends Controller
{

    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new MasEstadoVisibilidadModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function obtenerEstados($estado){

        $query = $this->repository->customQuery(
            function ($model) use ($estado) {
                return $model::where('estado', $estado)->where('id','<>', Constants::ESTADOS_VISIBILIDAD['oculto_todos'])->orderBy('id', 'asc')->get();
            }
        );

        return MasEstadoVisibilidadCollection::make($query);
    }
}
