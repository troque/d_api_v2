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
use App\Http\Resources\TbintDependenciaActuacion\TbintDependenciaActuacionCollection;
use App\Http\Resources\TbintDependenciaActuacion\TbintDependenciaActuacionResource;
use App\Models\DependenciaAccesoModel;
use App\Models\DependenciaConfiguracionModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\TbintDependenciaActuacionModel;
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

    public function getDependenciasActivas(Request $request)
    {
        $query = $this->repository->customQuery(
            function ($model) {
                return $model->where('estado', true)->orderBy("nombre", "asc")->get();
            }
        );
        return DependenciaOrigenCollection::make($query);
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


    public function getMasDependenciaOrigen($estado)
    {


        $query = $this->repository->customQuery(
            function ($model) use ($estado) {
                return $model->where("estado", $estado)->orderBy("nombre", "asc")->get();
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

            //Dependencias orientada a actuaciones
            TbintDependenciaActuacionModel::where('id_dependencia', $id)->delete();
            foreach($form['dependencia_actuaciones'] as $dependencia){
                //$dependenciaConfiguracion = new DependenciaConfiguracionModel();
                //DependenciaConfiguracionResource::make($dependenciaConfiguracion->create($depConf));
                $dependenciaActuacion['id_dependencia'] = $id;
                $dependenciaActuacion['id_dependencia_destino'] = $dependencia;
                $dependenciaActuacion["created_user"] = auth()->user()->name;
                //dd($dependenciaActuacion);
                TbintDependenciaActuacionModel::create($dependenciaActuacion);
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

    public function cargarDependenciasPorIdYNombre($idMasDependenciaAcceso)
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
                            (u.nombre || ' ' || u.apellido) AS nombre_jefe,
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
                $request['attributes']['nombre_jefe'] = $dependencia[$cont]->nombre_jefe;
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

    // Metodo encargado de traer si la dependencia tiene acceso a secretaria comun
    public function validarAccesoSecretariaComun($idDependenciaOrigen)
    {
        // Se capturan las variables
        $idDependenciaOrigen = $idDependenciaOrigen;

        // Se ejecuta la consulta
        $query = DB::select("SELECT PORCENTAJE_ASIGNACION, ID_DEPENDENCIA_ORIGEN, ID_DEPENDENCIA_ACCESO
                             FROM MAS_DEPENDENCIA_CONFIGURACION
                             WHERE ID_DEPENDENCIA_ORIGEN  = $idDependenciaOrigen AND
                                   ID_DEPENDENCIA_ACCESO = 12");

        // Se inicializa el array
        $data = [];

        // Se valida que haya informacion
        if (!empty($query)) {

            // Se añade el valor al array
            array_push(
                $data,
                [
                    "error" => false,
                    "mensajeError" => "Configuración valida",
                    "data" => $query[0]
                ]
            );
        } else {

            // Se añade el valor al array
            array_push(
                $data,
                [
                    "error" => true,
                    "mensajeError" => "No existe configuración para este tipo de dependencia",
                ]
            );
        }

        // Se retorna la informacion
        return $data[0];
    }


    public function getNombreDependenciaDuenaProceso($id_proceso_disciplinario)
    {
        /*$repository = new RepositoryGeneric();
        $repository->setModel(new ProcesoDiciplinarioModel());

        $query = $repository->customQuery($id_proceso_disciplinario
            function ($model) {
                return $model->where("uuid", $id_proceso_disciplinario)->where("estado", true)->where("id_etapa", ">", 2)->orderBy("nombre", "asc")->get();
            }
        );

        return DependenciaAccesoCollection::make($query);*/
    }

    public function puedeCrearActuaciones()
    {

        $query = DB::select("SELECT crear_actuaciones
            FROM mas_dependencia_origen
            WHERE id  = " . auth()->user()->id_dependencia);

        if ($query != null) {

            if ($query[0]->crear_actuaciones == 1) {
                $datos['crear_actuacion'] = true;
            } else {
                $datos['crear_actuacion'] = false;
            }

            $json['data']['attributes'] = $datos;
        }

        return $json;
    }

    public function cargarDependenciasConfiguracionActuacion($idDependencia)
    {
        $estado = true;
        $query = $this->repository->customQuery(
            function ($model) use ($estado, $idDependencia) {
                return $model
                    ->select(
                        'MAS_DEPENDENCIA_ORIGEN.id',
                        'MAS_DEPENDENCIA_ORIGEN.nombre',
                        'MAS_DEPENDENCIA_ORIGEN.estado',
                        'MAS_DEPENDENCIA_ORIGEN.id_usuario_jefe',
                        'MAS_DEPENDENCIA_ORIGEN.codigo_homologado',
                        'MAS_DEPENDENCIA_ORIGEN.prefijo',
                    )
                    ->leftJoin('TBINT_DEPENDENCIA_ACTUACION', 'TBINT_DEPENDENCIA_ACTUACION.id_dependencia_destino', '=', 'MAS_DEPENDENCIA_ORIGEN.id')
                    ->where('TBINT_DEPENDENCIA_ACTUACION.id_dependencia', $idDependencia)
                    ->where('MAS_DEPENDENCIA_ORIGEN.estado', $estado)
                    ->orderBy("MAS_DEPENDENCIA_ORIGEN.nombre", "asc")
                    ->get();
            }
        );


        return DependenciaOrigenCollection::make($query);
    }

    public function cargarDependenciasSecretariaComun()
    {
        $query = DB::select("
            SELECT
                mdo.id,
                mdo.nombre
            FROM
            mas_dependencia_origen mdo
            INNER JOIN mas_dependencia_configuracion mdc ON mdc.id_dependencia_origen = mdo.id
            INNER JOIN mas_dependencia_acceso mda ON mdc.id_dependencia_acceso = mda.id
            WHERE mda.nombre = 'Secretaria Común'
        ");

        return json_encode($query);
    }

    public function cargarDependenciasSegunConfiguracionRemisionQueja($idMasDependenciaAcceso)
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
                    ->join('MAS_DEPENDENCIA_CONFIGURACION', 'MAS_DEPENDENCIA_ORIGEN.ID', 'MAS_DEPENDENCIA_CONFIGURACION.ID_DEPENDENCIA_ORIGEN')
                    ->where('MAS_DEPENDENCIA_CONFIGURACION.ID_DEPENDENCIA_ACCESO', $idMasDependenciaAcceso)
                    ->where('MAS_DEPENDENCIA_ORIGEN.estado', $estado)
                    ->orderBy("MAS_DEPENDENCIA_ORIGEN.nombre", "asc")->get();
            }
        );


        return DependenciaOrigenCollection::make($query);
    }    

}
