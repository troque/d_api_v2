<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DependenciaOrigen\DependenciaOrigenCollection;
use App\Http\Resources\DependenciaOrigen\DependenciaOrigenResource;
use App\Http\Requests\DependenciaOrigenFormRequest;
use App\Models\DependenciaOrigenModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Resources\DependenciaOrigen\DependenciaAccesoCollection;
use App\Http\Resources\DependenciaOrigen\DependenciaConfiguracionResource;
use App\Models\DependenciaAccesoModel;
use App\Models\DependenciaConfiguracionModel;
use Illuminate\Support\Facades\DB;

class DependenciaOrigenController extends Controller
{

    private $repository;
    use LogTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new DependenciaOrigenModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $query = DependenciaOrigenModel::orderBy("nombre")->get();
        $estado = true;
        $query = $this->repository->customQuery(
            function ($model) use ($estado) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );
        return DependenciaOrigenCollection::make($query);
        // return DependenciaOrigenCollection::make($query);
        // return DependenciaOrigenCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function indexPaginate($paginaActual, $porPagina)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($porPagina, $paginaActual) {
                return DependenciaOrigenModel::orderBy('nombre', 'asc')->paginate($porPagina, ['*'], 'dependencias', $paginaActual);
            }
        );
        return  DependenciaOrigenCollection::make($query);
    }


    public function geDependenciaSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return DependenciaOrigenCollection::make($query);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DependenciaOrigenFormRequest $request)
    {
        try {
            $datos = $request->validated()["data"]["attributes"];
            DB::connection()->beginTransaction();

            $dep = DependenciaOrigenResource::make($this->repository->create($datos));

            if (isset($datos["accesos"])) {
                foreach ($datos["accesos"] as $depe) {

                    $dependenciaConfiguracion = new DependenciaConfiguracionModel();

                    $depConf["id_dependencia_origen"] = $dep["id"];
                    $depConf["id_dependencia_acceso"] = $depe;
                    $depConf["created_user"] = auth()->user()->name;
                    DependenciaConfiguracionResource::make($dependenciaConfiguracion->create($depConf));
                }
            }

            DB::connection()->commit();

            return $dep;
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return DependenciaOrigenResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DependenciaOrigenFormRequest $request, $id)
    {
        try {

            DB::connection()->beginTransaction();

            $form = $request->validated()["data"]["attributes"];
            if (empty($form["id_usuario_jefe"])) {

                $form["id_usuario_jefe"] = "";
            }

            //insertamos los roels
            DB::delete(DB::raw("delete from mas_dependencia_configuracion where id_dependencia_origen = :somevariable"), array(
                'somevariable' => $id,
            ));
            error_log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>");
            error_log(json_encode($form["porcentajes"]));
            //foreach ($form["accesos"] as $depe) {
            for ($cont = 0; $cont < count($form["accesos"]); $cont++) {

                $dependenciaConfiguracion = new DependenciaConfiguracionModel();

                $depConf["id_dependencia_origen"] = $id;
                $depConf["id_dependencia_acceso"] = $form["accesos"][$cont];
                $depConf["porcentaje_asignacion"] = $form["porcentajes"] != null && count($form["porcentajes"]) > $cont ? $form["porcentajes"][$cont] : null;
                $depConf["updated_user"] = auth()->user()->name;
                DependenciaConfiguracionResource::make($dependenciaConfiguracion->create($depConf));
            }

            $respuestaUsuario = DependenciaOrigenResource::make($this->repository->update($form, $id));

            DB::connection()->commit();
            return $respuestaUsuario;
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



    public function cargarDependenciasSegunConfiguracion($idMasDependenciaAcceso)
    {

        // error_log(json_encode($idMasDependenciaAcceso));
        $estado = true;
        $query = $this->repository->customQuery(
            function ($model) use ($estado, $idMasDependenciaAcceso) {
                return $model
                    ->select(
                        'MAS_DEPENDENCIA_ORIGEN.id',
                        'MAS_DEPENDENCIA_ORIGEN.nombre',
                        'MAS_DEPENDENCIA_ORIGEN.estado',
                        'MAS_DEPENDENCIA_ORIGEN.id_usuario_jefe',
                        'MAS_DEPENDENCIA_ORIGEN.codigo_homologado',
                        'MAS_DEPENDENCIA_ORIGEN.prefijo',
                    )
                    ->where('MAS_DEPENDENCIA_ORIGEN.estado', $estado)
                    ->orderBy("MAS_DEPENDENCIA_ORIGEN.nombre", "asc")->get();
            }
        );


        return DependenciaOrigenCollection::make($query);
    }


    public function cargarDependenciasEjeDisciplinario()
    {

        // VALIDAR SI PUEDE ATENDER UN TIPO DE EXPEDIENTE DE QUEJA INTERNA
        $dependencia = DB::select("select
                            mdo.nombre as nombre_dependencia,
                            mdc.id_dependencia_origen as id_dependencia_origen,
                            u.id AS id_usuario_jefe,
                            u.name AS nombre_usuario_jefe,
                            u.email
                            from mas_dependencia_configuracion mdc
                            inner join mas_dependencia_origen mdo on mdc.id_dependencia_origen = mdo.id
                            inner join users u on mdo.id_usuario_jefe = u.id
                            where mdc.id_dependencia_acceso = 10
                            and mdo.estado = 1");

        $lista = array();

        if (count($dependencia) > 0) {
            for ($cont = 0; $cont < count($dependencia); $cont++) {
                $request['type'] = "dependencia_instrucccion";
                $request['id'] = $dependencia[$cont]->id_dependencia_origen;
                $request['attributes']['nombre'] = $dependencia[$cont]->nombre_dependencia;
                $request['attributes']['id_usuario_jefe'] = $dependencia[$cont]->id_usuario_jefe;
                $request['attributes']['nombre_usuario_jefe'] = $dependencia[$cont]->nombre_usuario_jefe;
                $request['attributes']['nombre_solo_usuario_jefe'] = $dependencia[$cont]->nombre_usuario_jefe;
                $request['attributes']['estado'] = true;
                array_push($lista, $request);
            }
        }

        $json['data'] = $lista;

        return json_encode($json);
    }



    public function getDependenciasAcesso()
    {
        $accesosRepository = new RepositoryGeneric();
        $accesosRepository->setModel(new DependenciaAccesoModel());
        $query = $accesosRepository->customQuery(
            function ($model) {
                return $model->where("estado", true)->orderBy("nombre", "asc")->get();
            }
        );

        return DependenciaAccesoCollection::make($query);
    }
}
