<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParametroCamposCaratulasModel;
use App\Http\Requests\ParametroCamposCaratulasFormRequest;
use App\Http\Resources\ParametroCamposCaratulas\ParametroCamposCaratulaCollection;
use App\Http\Resources\ParametroCamposCaratulas\ParametroCamposCaratulaResource;
use App\Repositories\RepositoryGeneric;

class ParametroCamposCaratulasController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ParametroCamposCaratulasModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ParametroCamposCaratulaCollection::make($this->repository->paginate($request->limit ?? 100000));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ParametroCamposCaratulasFormRequest $request)
    {
        return ParametroCamposCaratulaResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ParametroCamposCaratulaResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ParametroCamposCaratulasFormRequest $request, $id)
    {
        return ParametroCamposCaratulaResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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


    // Metodo encargado de consultar todos los parametros de la caratula
    public function consultarParametrosCaratulas()
    {
        // Se consultan toda la informacion
        $parametros = $this->repository->all();

        // Se retorna los parametros
        return $parametros;
    }
}
