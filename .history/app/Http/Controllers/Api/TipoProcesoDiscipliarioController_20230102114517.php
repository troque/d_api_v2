<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoDerechoPeticionFormRequest;
use App\Http\Resources\TipoDerechoPeticion\TipoDerechoPeticionCollection;
use App\Http\Resources\TipoDerechoPeticion\TipoDerechoPeticionResource;
use App\Http\Utilidades\Constants;
use App\Models\ClasificacionRadicadoModel;
use App\Models\TipoDerechoPeticionModel;
use App\Models\TipoProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoProcesoDiscipliarioController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoProcesoDisciplinarioModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TipoProcesoDisciplinarioCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function geTipoDerechoPeticionSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return TipoProcesoDisciplinarioCollection::make($query);
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
    public function update(TipoProcesoDisciplinarioFormRequest $request,  $id)
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
}
