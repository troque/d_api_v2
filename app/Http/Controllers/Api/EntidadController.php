<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntidadFormRequest;
use App\Http\Resources\Entidad\EntidadCollection;
use App\Http\Resources\Entidad\EntidadResource;
use App\Http\Resources\Entidad\EntidadListResource;
use App\Models\EntidadModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class EntidadController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new EntidadModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = EntidadModel::query();
        $query = $query->leftJoin('sector', 'sector.idsector', '=', 'entidad.idsector')
            ->leftJoin('secretaria', 'secretaria.idsecretaria', '=', 'entidad.idsecretaria')
            ->select(
                'entidad.identidad',
                'entidad.nombre',
                'entidad.direccion',
                'sector.nombre as nombre_sector',
                'sector.idsector as id_sector',
                'secretaria.nombre as nombre_secretaria'
            )->orderBy('entidad.nombre', 'asc')->get();


        return EntidadCollection::make($query);
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\EntidadFormRequest  $request
     * @return App\Http\Resources\Entidad\EntidadResource
     */
    public function store(EntidadFormRequest $request): EntidadResource
    {
        return EntidadResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return EntidadResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EntidadFormRequest $request,  $id)
    {
        return EntidadResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
