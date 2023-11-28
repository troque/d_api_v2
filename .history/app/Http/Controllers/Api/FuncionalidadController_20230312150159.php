<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleFormRequest;
use App\Http\Resources\Role\FuncionalidadCollection;
use App\Http\Resources\Role\ModuloCollection;
use App\Http\Resources\Role\ModuloGrupoCollection;
use App\Models\Funcionalidad;
use App\Models\Modulo;
use App\Models\ModuloGrupoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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


    public function getModulos2($id)
    {

        $query_funcionalidades = DB::select("
            select
            mm.id as id_modulo,
            mf.id as id_funcionalidad,
            mf.nombre as nombre_funcionalidad,
            (select fr.funcionalidad_id from funcionalidad_rol fr where fr.funcionalidad_id = mf.id and fr.role_id = " . $id . ") as funcionalidad
            from mas_modulo mm
            inner join mas_funcionalidad mf on mf.id_modulo = mm.id");


        for ($cont = 0; $cont < count($query_funcionalidades); $cont++) {

            if ($query_funcionalidades[$cont]->funcionalidad == '') {
                $query_funcionalidades[$cont]->funcionalidad = false;
            } else {
                $query_funcionalidades[$cont]->funcionalidad = true;
            }
        }

        $array = array();
        $reciboDatos['type'] = "permisos";
        $reciboDatos['id'] = $id;
        $reciboDatos['attributes']['funcionalidades'] = $query;
        array_push($array, $reciboDatos);

        $json['data'] = $array;
        return json_encode($json);

        //return ModuloCollection::make($query);
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

    /**
     *
     */
    public function getGruposPermisos()
    {
        $repository_modulo = new RepositoryGeneric();
        $repository_modulo->setModel(new ModuloGrupoModel());
        $query = $repository_modulo->customQuery(function ($model) {
            return
                $model->where('estado', true)
                ->orderBy('orden')
                ->get();
        });
        return ModuloGrupoCollection::make($query);
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
