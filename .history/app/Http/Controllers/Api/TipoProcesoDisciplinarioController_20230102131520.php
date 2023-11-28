<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoQuejaFormRequest;
use App\Http\Resources\TipoProcesoDisciplinario\TipoProcesoDisciplinarioCollection;
use App\Models\TipoProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TipoProcesoDisciplinarioController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoProcesoDisciplinarioModel());
    }


    public function index(Request $request)
    {

        return TipoProcesoDisciplinarioCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTipoProcesoDiciplinario(Request $request)
    {
        $array = array(); //creamos un array


        $datos['type'] = "mas_tipo_proceso_disciplinario";
        $datos['id'] = "0";
        $datos['attributes']['nombre'] = "Proceso Disciplinario";
        $datos['attributes']['observacion'] = "Proceso Disciplinario";
        $datos['attributes']['estado'] = 1;


        array_push($array, $datos);

        $json['data'] = $array;
        return json_encode($json);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TipoQuejaFormRequest $request)
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
        return TipoProcesoDisciplinarioResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoQuejaFormRequest $request, $id)
    {
        return TipoProcesoDisciplinarioResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
    public function getTiposQuejaHabilitados($id_proceso_disciplinario)
    {
    }

    public function getTiposQueja()
    {
    }
}
