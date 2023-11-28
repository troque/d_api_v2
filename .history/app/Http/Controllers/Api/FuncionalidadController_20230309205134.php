<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleFormRequest;
use App\Http\Resources\Role\FuncionalidadCollection;
use App\Http\Resources\Role\ModuloCollection;

use App\Models\Funcionalidad;
use App\Models\Modulo;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class FuncionalidadController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new Funcionalidad());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = Funcionalidad::orderBy("nombre")->get();
        return FuncionalidadCollection::make($query);
    }

    public function getModulos()
    {
        $repository_modulo = new RepositoryGeneric();
        $repository_modulo->setModel(new Modulo());
        $query = $repository_modulo->customQuery(function ($model) {
            return
                $model->where('estado', true)
                ->orderBy('id_mas_grupo')
                ->orderBy('order')
                ->get();
        });
        return ModuloCollection::make($query);
    }

    public function getFuncionalidadByModulo($nombre_funcionalidad)
    {
        $repository_modulo = new RepositoryGeneric();
        $repository_modulo->setModel(new Modulo());
        $query = $repository_modulo->customQuery(function ($model) use ($nombre_funcionalidad) {
            return
                $model->where('nombre', $nombre_funcionalidad)
                ->where('estado', true)
                ->get();
        });
        return ModuloCollection::make($query);
    }

    public function getGruposPermisos($nombre_funcionalidad)
    {
        $repository_modulo = new RepositoryGeneric();
        $repository_modulo->setModel(new Modulo());
        $query = $repository_modulo->customQuery(function ($model) use ($nombre_funcionalidad) {
            return
                $model->where('nombre', $nombre_funcionalidad)
                ->where('estado', true)
                ->get();
        });
        return ModuloCollection::make($query);
    }



    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\RoleFormRequest  $request
     * @return App\Http\Resources\Role\RoleResource
     */
    public function store(RoleFormRequest $request)
    {
        //return RoleResource::make($this->repository->create($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //return RoleWithFuncionalidadesResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleFormRequest $request,  $id)
    {
        //return RoleResource::make($this->repository->update($request->validated(), $id));
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
