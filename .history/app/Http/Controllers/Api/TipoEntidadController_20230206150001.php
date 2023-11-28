<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoEntidadFormRequest;
use App\Http\Resources\TipoEntidad\TipoEntidadCollection;
use App\Http\Resources\TipoEntidad\TipoEntidadResource;
use App\Models\TipoEntidadModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TipoEntidadController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoEntidadModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = TipoEntidadModel::query();
        $query = $query->select('mas_tipo_entidad.id', 'mas_tipo_entidad.nombre', 'mas_tipo_entidad.estado')->orderBy('mas_tipo_entidad.nombre', 'asc')->get();

        return TipoEntidadCollection::make($query);
        //return CiudadCollection::make($query);

        //return TipoEntidadListResource::collection($this->repository->orderBy($request->get('nombre')));
        //return TipoEntidadCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\TipoEntidadFormRequest  $request
     * @return App\Http\Resources\TipoEntidad\TipoEntidadResource
     */
    public function store(TipoEntidadFormRequest $request): TipoEntidadResource
    {
        return TipoEntidadResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TipoEntidadResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoEntidadFormRequest $request,  $id)
    {
        return TipoEntidadResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
