<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Controllers\Traits\ReclasificacionTrait;
use App\Http\Controllers\Traits\RepartoAleatorioTrait;
use App\Http\Requests\ClasificacionRadicadoFormRequest;
use App\Http\Resources\ClasificacionRadicado\ClasificacionRadicadoCollection;
use App\Http\Resources\ClasificacionRadicado\ClasificacionRadicadoResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioCollection;
use App\Http\Resources\ValidarClasificacion\ValidarClasificacionResource;
use App\Http\Utilidades\Constants;
use App\Models\ClasificacionRadicadoModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ValidarClasificacionModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class ClasificacionRadicadoController extends Controller
{

    private $repository;
    use LogTrait;
    use RepartoAleatorioTrait;
    use ReclasificacionTrait;


    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ClasificacionRadicadoModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ClasificacionRadicadoCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClasificacionRadicadoFormRequest $request)
    {
        try {
            $datosRequest = $request->validated()["data"]["attributes"];

            DB::connection()->beginTransaction();

            $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;
            $datosRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);

            // VALIDAR SI ES CONTROL INTERNO
            $control_interno = DB::select("
                select
                    id_dependencia_origen
                from
                    mas_dependencia_configuracion
                where id_dependencia_origen = " . auth()->user()->id_dependencia . "
                and id_dependencia_acceso = 9");


            if (count($control_interno) > 0) {
                $datosRequest['id_tipo_queja'] = Constants::TIPO_QUEJA['interna'];
            }

            //VALIDA SI ES JEFE
            $jefe = DB::select("
                select
                    name as nombre_funcionario
                from
                    users
                inner join mas_dependencia_origen on users.id = mas_dependencia_origen.id_usuario_jefe
                where mas_dependencia_origen.id = " . auth()->user()->id_dependencia . "
                and users.name = '" . auth()->user()->name . "'
            ");


            if ($jefe == null) {

                // SE REGISTRA CLASIFICACION --- ETAPA CAPTURA Y REPARTO
                if ($datosRequest['id_etapa'] == Constants::ETAPA['captura_reparto']) {

                    // ACTUALIZA TODOS EL HISTORIAL EN INACTIVO PARA DEJAR SOLAMENTE EL ÚLTIMO COMO ACTIVO.
                    ClasificacionRadicadoModel::where('estado', 1)->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['estado' => 0]);

                    if ($datosRequest['fecha_termino']) {
                        $datosRequest['fecha_termino'] = Carbon::parse($datosRequest['fecha_termino'])->format('Y-m-d');
                    }

                    // REGISTRA EL ULTIMO PROCESO
                    $clasificacion = ClasificacionRadicadoResource::make($this->repository->create($datosRequest));
                    $array = json_decode(json_encode($clasificacion));

                    // REGISTRA LA INFORMACIÓN EN EL LOG
                    $datosRequest['id_fase'] = Constants::FASE['clasificacion_radicado'];
                    $datosRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['clasificacion_expediente'];

                    error_log("TIPO DE TRANSACCION: " . $datosRequest['id_tipo_transaccion']);

                    $respuesta = ClasificacionRadicadoController::storeLogClasificacionExpediente($datosRequest, auth()->user()->id_dependencia, auth()->user()->name, $array->id, false);

                    // REGISTRA EL ULTIMO RADICADO.
                    DB::connection()->commit();
                    return $respuesta;
                }
                // SE VALIDA EVALUACIÓN DE LA CLASIFICACION --- ETAPA EVALUACION
                elseif ($datosRequest['id_etapa'] == Constants::ETAPA['evaluacion']) {

                    // APLICA RECLASIFICACION == SI
                    if ($datosRequest['reclasificacion']) {

                        error_log("APLICA RECLASIFICACION");

                        // ACTUALIZA TODOS EL HISTORIAL EN INACTIVO PARA DEJAR SOLAMENTE EL ÚLTIMO COMO ACTIVO.
                        ClasificacionRadicadoModel::where('estado', 1)->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['estado' => 0]);

                        if ($datosRequest['fecha_termino']) {
                            $datosRequest['fecha_termino'] = Carbon::parse($datosRequest['fecha_termino'])->format('Y-m-d');
                        }

                        $num_reclasificaciones = DB::select(
                            "
                            select
                                count(*) as num_reclasificacion
                            from
                                log_proceso_disciplinario
                            where id_proceso_disciplinario = '" . $datosRequest['id_proceso_disciplinario'] . "'
                            and id_tipo_transaccion = " . Constants::TIPO_DE_TRANSACCION['reclasificacion_expediente']
                        );

                        error_log("NUMERO DE RECLSAIFICACIONES: " . $num_reclasificaciones[0]->num_reclasificacion);

                        if ($num_reclasificaciones[0]->num_reclasificacion >= Constants::INTENTOS['num_reclasificaciones']) {

                            // SE ASIGNA AL JEFE DE LA DELEGADA
                            $funcionario_asignado = DB::select(
                                "
                                select
                                    u.name as nombre_funcionario,
                                    u.nombre,
                                    u.apellido,
                                    u.estado,
                                    u.reparto_habilitado
                                from
                                    users u
                                inner join mas_dependencia_origen on u.id = mas_dependencia_origen.id_usuario_jefe
                                where mas_dependencia_origen.id = " . auth()->user()->id_dependencia
                            );

                            /*$aux = "select
                                    u.name as nombre_funcionario,
                                    u.nombre,
                                    u.apellido,
                                    u.estado,
                                    u.reparto_habilitado
                                from
                                    users u
                                inner join mas_dependencia_origen on users.id = mas_dependencia_origen.id_usuario_jefe
                                where mas_dependencia_origen.id = " . auth()->user()->id_dependencia;
                             error_log($aux);*/

                            if (count($funcionario_asignado) <= 0) {
                                $error = new stdClass;
                                $error->estado = false;
                                $error->error = 'La dependencia no tiene usuario JEFE asignado';
                                return $error;
                            } else if (!$funcionario_asignado[0]->estado) {
                                $error = new stdClass;
                                $error->estado = false;
                                $error->error = 'El usuario JEFE (' . $funcionario_asignado[0]->nombre_funcionario . ' - ' . $funcionario_asignado[0]->nombre . ' ' . $funcionario_asignado[0]->apellido . ') de la dependencia no se encuentra activo';
                                return $error;
                            } else if (!$funcionario_asignado[0]->reparto_habilitado) {
                                $error = new stdClass;
                                $error->estado = false;
                                $error->error = 'El usuario JEFE (' . $funcionario_asignado[0]->nombre_funcionario . ' - ' . $funcionario_asignado[0]->nombre . ' ' . $funcionario_asignado[0]->apellido . ') de la dependencia no se encuentra activo';
                                return $error;
                            }


                            $funcionario_asignado = $funcionario_asignado[0]->nombre_funcionario;

                            // REGISTRA EL ULTIMO RADICADO.
                            $clasificacion = ClasificacionRadicadoResource::make($this->repository->create($datosRequest));
                            $array = json_decode(json_encode($clasificacion));

                            // LOG PROCESO DISCIPLINARIO
                            $datosRequest['id_fase'] = Constants::FASE['validacion_clasificacion'];
                            $datosRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['reclasificacion_expediente'];
                            $respuesta = ClasificacionRadicadoController::storeLogClasificacionExpediente($datosRequest, auth()->user()->id_dependencia, $funcionario_asignado, $array->id, true);

                            // error_log(json_encode($respuesta));
                            // error_log(json_encode($respuesta));

                            DB::connection()->commit();
                            return $respuesta;
                        } else {
                            // REGISTRA EL ULTIMO RADICADO.
                            $clasificacion = ClasificacionRadicadoResource::make($this->repository->create($datosRequest));
                            $array = json_decode(json_encode($clasificacion));

                            // PRIMERO VALIDA QUE EL USUARIO ACTUAL TENGA LOS PERMISOS
                            $funcionario_asignado = ClasificacionRadicadoController::storeRepartoDirigidoValidarClasificacion(auth()->user()->name,  $array->id);

                            if ($funcionario_asignado != null) {
                                // SI TIENE LOS PERMISOS
                                $funcionario_asignado = $funcionario_asignado->nombre_funcionario;
                            } else {
                                // REPARTO Y BALANCEO DE CARGA
                                $funcionario_asignado = ClasificacionRadicadoController::storeRepartoAleatorio($datosRequest['created_user'], $datosRequest['id_proceso_disciplinario'], auth()->user()->id_dependencia);
                                $funcionario_asignado = $funcionario_asignado->nombre_funcionario;
                            }

                            // LOG PROCESO DISCIPLINARIO
                            $datosRequest['id_fase'] = Constants::FASE['validacion_clasificacion'];
                            $datosRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['reclasificacion_expediente'];
                            $respuesta = ClasificacionRadicadoController::storeLogClasificacionExpediente($datosRequest, auth()->user()->id_dependencia, $funcionario_asignado, $array->id, true);

                            DB::connection()->commit();
                            return $respuesta;
                        }
                    }
                    // APLICA RECLASIFICACION == NO
                    else {

                        // error_log("SE INGRESA NO APLICA RECLASIFIFCACION");

                        // VALIDAR CLASIFICACION
                        $clasificacion = DB::select("
                            select
                                uuid
                            from
                                clasificacion_radicado
                            where id_proceso_disciplinario = '" . $datosRequest['id_proceso_disciplinario'] . "'
                            and estado = 1
                        ");

                        // REGISTRA EL ULTIMO RADICADO.
                        $datosRequest['validacion_jefe'] = false;
                        $datosRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['clasificacion_expediente'];
                        $respuesta = ClasificacionRadicadoController::storeLogClasificacionExpediente($datosRequest, auth()->user()->id_dependencia, auth()->user()->name, $clasificacion[0]->uuid, true);

                        $validarClasificacionRequest['id_clasificacion_radicado'] = $clasificacion[0]->uuid;
                        $validarClasificacionRequest['id_etapa'] = $datosRequest['id_etapa'];
                        $validarClasificacionRequest['estado'] = 1;
                        $validarClasificacionRequest['created_user'] = $datosRequest['created_user'];
                        $validarClasificacionRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];

                        $validarClasificacionModel = new ValidarClasificacionModel();
                        ValidarClasificacionResource::make($validarClasificacionModel->create($validarClasificacionRequest));

                        // REGISTRA EL ULTIMO RADICADO.
                        DB::connection()->commit();
                        return $respuesta;
                    }
                }
            } else {

                // CUANDO EL JEFE ES EL QUE ASIGNA DIRECTAMENTE EL CASO

                $validacion_previa = DB::select("select uuid from validar_clasificacion where id_proceso_disciplinario = '" . $datosRequest['id_proceso_disciplinario'] . "'");

                if ($validacion_previa != null) {
                    $this->reclasificacionPorTipoExpediente($datosRequest['id_proceso_disciplinario']);
                    $datosRequest['observaciones'] = "Reclasificacion por tipo de expediente";
                    $datosRequest['id_fase'] = Constants::FASE['ninguna'];
                    $datosRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['reclasificacion_expediente'];

                    $this->storeLogClasificacionExpediente($datosRequest, auth()->user()->id_dependencia, null, null, true);
                }

                // ACTUALIZA TODOS EL HISTORIAL EN INACTIVO PARA DEJAR SOLAMENTE EL ÚLTIMO COMO ACTIVO.
                ClasificacionRadicadoModel::where('estado', 1)->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['estado' => 0]);

                if ($datosRequest['fecha_termino']) {
                    $datosRequest['fecha_termino'] = Carbon::parse($datosRequest['fecha_termino'])->format('Y-m-d');
                }

                $datosRequest['validacion_jefe'] = true;

                // REGISTRA LA NUEVA CLASIFICACIÓN DEL RADICADO
                $clasificacion = ClasificacionRadicadoResource::make($this->repository->create($datosRequest));
                $array = json_decode(json_encode($clasificacion));

                $query = DB::select("
                select
                    id_funcionario_asignado,
                    created_at
                from
                    log_proceso_disciplinario
                where
                    id_proceso_disciplinario ='" . $datosRequest['id_proceso_disciplinario'] . "'
                and
                    (id_tipo_transaccion = " . Constants::TIPO_DE_TRANSACCION['clasificacion_expediente'] . "
                or
                    id_tipo_transaccion = " . Constants::TIPO_DE_TRANSACCION['reclasificacion_expediente'] . ")
                and
                    id_funcionario_asignado != '" . auth()->user()->name . "' order by created_at");

                for ($cont = 0; $cont < count($query); $cont++) {
                    // PRIMERO VALIDA QUE EL USUARIO ACTUAL TENGA LOS PERMISOS
                    $funcionario_asignado = ClasificacionRadicadoController::storeRepartoDirigidoValidarClasificacion($query[$cont]->id_funcionario_asignado, $array->id);

                    if ($funcionario_asignado != null) {
                        // SI TIENE LOS PERMISOS
                        $funcionario_asignado = $funcionario_asignado->nombre_funcionario;
                        $cont = count($query);
                    }
                }

                //if($funcionario_asignado == null){
                // REPARTO Y BALANCEO DE CARGA
                $funcionario_asignado = ClasificacionRadicadoController::storeRepartoAleatorio($datosRequest['created_user'], $datosRequest['id_proceso_disciplinario'], auth()->user()->id_dependencia);

                if ($funcionario_asignado == null) {
                    DB::connection()->rollBack();
                    $error['estado'] = false;
                    $error['error'] = 'La dependencia actual no tiene funcionarios que puedan atender este tipo de expediente.';
                    return json_encode($error);
                }

                $funcionario_asignado = $funcionario_asignado->nombre_funcionario;
                //}

                // LOG PROCESO DISCIPLINARIO
                // error_log("ESTOY HACIENDO UNA RECLASIFICACION");
                $datosRequest['id_fase'] = Constants::FASE['validacion_clasificacion'];
                // error_log("==================================");
                $datosRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['reclasificacion_expediente'];
                $respuesta = ClasificacionRadicadoController::storeLogClasificacionExpediente($datosRequest, auth()->user()->id_dependencia, $funcionario_asignado, $array->id, true);

                // SE VALIDA EL EXPEDIENTE
                $validarClasificacionRequest['id_clasificacion_radicado'] = $array->id;
                $validarClasificacionRequest['id_etapa'] = $datosRequest['id_etapa'];
                $validarClasificacionRequest['estado'] = 1;
                $validarClasificacionRequest['created_user'] = $datosRequest['created_user'];
                $validarClasificacionRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
                $validarClasificacionRequest['eliminado'] = false;

                $validarClasificacionModel = new ValidarClasificacionModel();
                ValidarClasificacionResource::make($validarClasificacionModel->create($validarClasificacionRequest));

                // REGISTRA EL ULTIMO RADICADO.
                DB::connection()->commit();
                return $respuesta;
            }
        } catch (\Exception $e) {
            error_log($e);
            //dd($e);
            // Woopsy
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }


    /**
     *
     */
    public function asignacionClasificacionByJefe(ClasificacionRadicadoFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        $datosRequest['validacion_jefe'] = true;

        //echo($datosRequest['uuid']);

        $log = new RepositoryGeneric();
        $log->setModel(new LogProcesoDisciplinarioModel());
        $query = $log->customQuery(function ($model) use ($datosRequest) {
            return
                $model->where('id_fase_registro', $datosRequest['uuid'])->get();
        });

        // VALIDA SI EL USUARIO ASIGNADO ES JEFE
        if (count($query) > 0 && $query[0]->id_funcionario_actual == auth()->user()->name) {
            $funcionario_asignado = $query[0]->id_funcionario_registra;
        } else {

            // Primero valida que el usuario que reclasifico tenga permisos

            $funcionario_asignado = ClasificacionRadicadoController::storeRepartoDirigidoValidarClasificacion($query[0]->id_funcionario_registra, $datosRequest['uuid']);

            if ($funcionario_asignado != null) {
                $funcionario_asignado = $funcionario_asignado->nombre_funcionario;
            } else {
                $funcionario_asignado = $query[0]->id_funcionario_asignado;
            }
        }

        // VALIDAR CLASIFICACION
        $validarClasificacionRequest['id_clasificacion_radicado'] = $datosRequest['uuid'];
        $validarClasificacionRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
        $validarClasificacionRequest['id_etapa'] = $datosRequest['id_etapa'];
        $validarClasificacionRequest['estado'] = 1;
        $validarClasificacionRequest['created_user'] = $funcionario_asignado;
        $validarClasificacionRequest['eliminado'] = 0;

        // VALIDA LA CLASIFICACION
        $validarClasificacionModel = new ValidarClasificacionModel();
        ValidarClasificacionResource::make($validarClasificacionModel->create($validarClasificacionRequest));

        // ACTUALIZA TODOS EL HISTORIAL EN INACTIVO PARA DEJAR SOLAMENTE EL ÚLTIMO COMO ACTIVO.
        ClasificacionRadicadoModel::where('estado', 1)->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['estado' => 0]);
        ClasificacionRadicadoModel::where('uuid', $datosRequest['uuid'])->update(['estado' => 1, 'validacion_jefe' => true]);

        LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);
        LogProcesoDisciplinarioModel::where('id_fase_registro', $datosRequest['uuid'])->update(['id_funcionario_actual' => $funcionario_asignado, 'id_funcionario_asignado' => $funcionario_asignado]);

        // CREAR NUEVA INSTANCIA
        $log = new RepositoryGeneric();
        $log->setModel(new LogProcesoDisciplinarioModel());
        $query = $log->customQuery(function ($model) use ($datosRequest) {
            return
                $model->where('id_fase_registro', $datosRequest['uuid'])->get();
        });

        return LogProcesoDisciplinarioCollection::make($query);
    }


    /**
     *
     */
    public function getReclasificacion($procesoDiciplinarioUUID)
    {

        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID) {
            return $model->where('clasificacion_radicado.id_proceso_disciplinario', $procesoDiciplinarioUUID)
                ->where('clasificacion_radicado.reclasificacion', true)
                ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_fase_registro', '=', 'clasificacion_radicado.uuid')
                ->select(
                    'clasificacion_radicado.uuid',
                    'clasificacion_radicado.id_proceso_disciplinario',
                    'clasificacion_radicado.id_etapa',
                    'clasificacion_radicado.id_tipo_expediente',
                    'clasificacion_radicado.observaciones',
                    'clasificacion_radicado.id_tipo_queja',
                    'clasificacion_radicado.id_termino_respuesta',
                    'clasificacion_radicado.fecha_termino',
                    'clasificacion_radicado.hora_termino',
                    'clasificacion_radicado.gestion_juridica',
                    'clasificacion_radicado.estado',
                    'clasificacion_radicado.id_estado_reparto',
                    'clasificacion_radicado.created_user',
                    'clasificacion_radicado.created_at',
                    'clasificacion_radicado.oficina_control_interno',
                    'clasificacion_radicado.id_tipo_derecho_peticion',
                    'clasificacion_radicado.reclasificacion',
                    'clasificacion_radicado.id_dependencia',
                    'clasificacion_radicado.validacion_jefe'
                )
                ->orderBy('clasificacion_radicado.created_at', 'desc')
                ->get();
        });

        return ClasificacionRadicadoCollection::make($query);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ClasificacionRadicadoResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_proceso_disciplinario)
    {
        return ClasificacionRadicadoResource::make($this->repository->update($request->validated()["data"]["attributes"], $id_proceso_disciplinario));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     *
     */
    public function getClasificacionRadicadoByIdDisciplinario($procesoDiciplinarioUUID, ClasificacionRadicadoFormRequest $request)
    {

        $datosRequest = $request->validated()["data"]["attributes"];

        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID, $datosRequest) {
            return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)
                ->where('estado', $datosRequest['estado'])
                ->orderBy('created_at', 'desc')->get();
        });

        return ClasificacionRadicadoCollection::make($query);
    }

    /**
     *
     */
    public function getClasificacionRadicadoFilter($procesoDiciplinarioUUID, ClasificacionRadicadoFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        //error_log(json_encode($datosRequest));
        //error_log(json_encode($procesoDiciplinarioUUID));

        $query = ClasificacionRadicadoModel::query();
        $query = $query->where('id_proceso_disciplinario', $procesoDiciplinarioUUID);
        $query = $query->leftJoin('proceso_disciplinario', 'id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid');

        if (!empty($datosRequest['id_tipo_expediente']) && $datosRequest['id_tipo_expediente'] != "-1") {
            //$query = $query->where('tipo_documento', 'like', '%' . $datosRequest['tipo_documento'] . '%');
            $query = $query->where('id_tipo_expediente', '=', $datosRequest['id_tipo_expediente']);
        }
        if (!empty($datosRequest['id_tipo_queja']) && $datosRequest['id_tipo_queja'] != "-1") {
            $query = $query->where('id_tipo_queja', '=', $datosRequest['id_tipo_queja']);
        }
        if (!empty($datosRequest['id_termino_respuesta']) && $datosRequest['id_termino_respuesta'] != "-1") {
            $query = $query->where('id_termino_respuesta', '=', $datosRequest['id_termino_respuesta']);
        }
        if (!empty($datosRequest['observaciones']) && $datosRequest['observaciones'] != "-1") {
            $query = $query->where('observaciones', 'like', '%' . $datosRequest['observaciones'] . '%');
            //$query = $query->where('observaciones', '=', $datosRequest['observaciones']);
        }

        $query = $query->select(
            'clasificacion_radicado.uuid',
            'clasificacion_radicado.id_proceso_disciplinario',
            'clasificacion_radicado.id_etapa',
            'clasificacion_radicado.id_tipo_expediente',
            'clasificacion_radicado.observaciones',
            'clasificacion_radicado.id_tipo_queja',
            'clasificacion_radicado.id_termino_respuesta',
            'clasificacion_radicado.fecha_termino',
            'clasificacion_radicado.hora_termino',
            'clasificacion_radicado.gestion_juridica',
            'clasificacion_radicado.estado',
            'clasificacion_radicado.id_estado_reparto',
            'clasificacion_radicado.validacion_jefe',
        )
            ->orderBy('clasificacion_radicado.created_at', 'desc')->get();

        return ClasificacionRadicadoCollection::make($query);

        if (empty($queryAntecendente[0])) {
            $error['estado'] = false;
            $error['error'] = 'No se encontro información relacionada, si el error persiste consulte con el Administrador TICS';
            return json_encode($error);
        }

        return ClasificacionRadicadoCollection::make($queryAntecendente);
    }


    /**
     *
     */
    public function getValidarClasificadoPorJefe($id_proceso_disciplinario)
    {

        $query = $this->repository->customQuery(function ($model) use ($id_proceso_disciplinario) {
            return $model->where('id_proceso_disciplinario', $id_proceso_disciplinario)
                ->where('estado', 1)
                ->where('validacion_jefe', 1)
                ->get();
        });


        if (!empty($query[0])) {
            $datos['validacion_jefe'] = true;
            $json['data']['attributes'] = $datos;

            return $json;
        } else {
            $error['estado'] = false;
            $error['error'] = 'NA';

            return $error;
        }
    }
}
