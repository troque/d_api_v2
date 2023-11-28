<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleFormRequest;
use App\Http\Resources\Role\FuncionalidadRolResource as RoleFuncionalidadRolResource;
use App\Http\Resources\Role\RoleCollection;
use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\Role\RoleWithFuncionalidadesResource;
use App\Models\FuncionalidadRol;
use App\Models\Role;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class RoleController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new Role());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = Role::orderBy("name")->get();
        return RoleCollection::make($query);
        //return RoleCollection::make($this->repository->paginate($request->limit ?? 10));
    }


    public function indexPaginate($paginaActual, $porPagina)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($porPagina, $paginaActual) {
                return Role::orderBy('name', 'asc')->paginate($porPagina, ['*'], 'roles', $paginaActual);
            }
        );
        return  RoleCollection::make($query);
    }

    public function allRolesGestorRespuesta()
    {
        /*$query = $this->repository->customQuery(
            function ($model) {
                return Role::where('estado',true);
            }
        );*/
        $query = Role::orderBy("name")->get();

        if ($query->count() <= 0) {
            $error = new stdClass;
            $error->estado = false;
            $error->error = 'No hay roles registrados en el sistema';
            return $error;
        }

        return RoleCollection::make($query);
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\RoleFormRequest  $request
     * @return App\Http\Resources\Role\RoleResource
     */
    public function store(RoleFormRequest $request)
    {

        try {
            DB::connection()->beginTransaction();
            $datos = $request->validated()["data"]["attributes"];
            //buscamos si no hay otro ya registrado

            $queryYaExiste = $this->repository->customQuery(function ($model) use ($datos) {
                return
                    $model->where('name', $datos["nombre"])
                    ->get();
            });

            if (!empty($queryYaExiste[0])) {
                $error['estado'] = false;
                $error['error'] = 'El nombre de rol ' . $datos["nombre"] . ' ya existe en el sistema, digite otro por favor.';
                return json_encode($error);
            }



            $datosRol["name"] = $datos["nombre"];
            $datosRol["created_user"] = auth()->user()->name;
            $respuestaRol = RoleResource::make($this->repository->create($datosRol));

            //insertamos las funcionalidades
            foreach ($datos["funcionalidades"] as $funcionalidad) {

                $rolFuncionalidadModel = new FuncionalidadRol();

                $funcionalidadRol["ROLE_ID"] = $respuestaRol["id"];
                $funcionalidadRol["FUNCIONALIDAD_ID"] = $funcionalidad;
                RoleFuncionalidadRolResource::make($rolFuncionalidadModel->create($funcionalidadRol));
            }

            DB::connection()->commit();
            return $respuestaRol;
        } catch (\Exception $e) {
            // Woopsy
            //dd($e);
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return RoleWithFuncionalidadesResource::make($this->repository->find($id));
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
        try {
            DB::connection()->beginTransaction();
            $datos = $request->validated()["data"]["attributes"];

            $queryYaExiste = $this->repository->customQuery(function ($model) use ($datos, $id) {
                return
                    $model->where('name', $datos["nombre"])
                    ->where('id', '<>', $id)
                    ->get();
            });

            if (!empty($queryYaExiste[0])) {
                $error['estado'] = false;
                $error['error'] = 'El nombre de rol ' . $datos["nombre"] . ' ya existe en el sistema, digite otro por favor.';
                return json_encode($error);
            }

            $datosRol["name"] = $datos["nombre"];;
            $respuestaRol = RoleResource::make($this->repository->update($datosRol, $id));

            //insertamos los roels
            DB::delete(DB::raw("delete from FUNCIONALIDAD_ROL where ROLE_ID = :somevariable"), array(
                'somevariable' => $id,
            ));

            foreach ($datos["funcionalidades"] as $funcionalidad) {

                $rolFuncionalidadModel = new FuncionalidadRol();

                $funcionalidadRol["ROLE_ID"] = $respuestaRol["id"];
                $funcionalidadRol["FUNCIONALIDAD_ID"] = $funcionalidad;
                RoleFuncionalidadRolResource::make($rolFuncionalidadModel->create($funcionalidadRol));
            }

            DB::connection()->commit();
            return $respuestaRol;
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
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
