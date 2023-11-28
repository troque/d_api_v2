<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\RepositoryGeneric;
use App\Http\Requests\CargosFormRequest;
use App\Http\Resources\Cargos\CargosCollection;
use App\Http\Resources\Cargos\CargosResource;
use App\Models\CargosModel;

class CargosController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new CargosModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Se inicializa la consulta
        $query = $this->repository->customQuery(
            function ($model) {
                return $model->where('estado', true)->orderBy("id", "asc")->get();
            }
        );

        // Se retorna la consulta
        return CargosCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CargosFormRequest $request)
    {
        return CargosResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return CargosResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CargosFormRequest $request, $id)
    {
        return CargosResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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

    /**
     * Metodo encargado de cargar los cargos sin estado
     */
    public function getCargos($estado)
    {
        // Se inicializa el query
        $query = $this->repository->customQuery(
            function ($model) use ($estado) {
                return $model->where("estado", $estado)->orderBy("id", "asc")->get();
            }

        );

        // Se retorna
        return CargosCollection::make($query);
    }
}
