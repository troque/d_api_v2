<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoInteresadoFormRequest;
use App\Http\Resources\TipoInteresado\TipoInteresadoCollection;
use App\Http\Resources\TipoInteresado\TipoInteresadoResource;
use App\Models\TipoInteresadoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TipoInteresadoController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoInteresadoModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = TipoInteresadoModel::query();
        $query = $query->where("estado", true)->select('mas_tipo_interesado.id, mas_tipo_interesado.nombre, mas_tipo_interesado.estado')->orderBy('mas_tipo_interesado.nombre', 'desc')->get();

        return TipoInteresadoCollection::make($query);
        //return CiudadCollection::make($query);

        //return TipoInteresadoListResource::collection($this->repository->orderBy($request->get('nombre')));
        //return TipoInteresadoCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\TipoInteresadoFormRequest  $request
     * @return App\Http\Resources\TipoInteresado\TipoInteresadoResource
     */
    public function store(TipoInteresadoFormRequest $request): TipoInteresadoResource
    {
        return TipoInteresadoResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TipoInteresadoResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoInteresadoFormRequest $request,  $id)
    {
        return TipoInteresadoResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
