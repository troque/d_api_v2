<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Controllers\Traits\NumeroCasosTrait;
use App\Http\Controllers\Traits\PermisosExpedienteTrait;
use App\Http\Controllers\Traits\ReclasificacionTrait;
use App\Http\Controllers\Traits\RepartoAleatorioParametrizadoTrait;
use App\Http\Requests\EvaluacionFormRequest;
use App\Http\Resources\Evaluacion\EvaluacionCollection;
use App\Http\Resources\Evaluacion\EvaluacionResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Resources\RemisionQueja\RemisionQuejaResource;
use App\Http\Resources\TipoConductaProcesoDisciplinario\TipoConductaProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\ClasificacionRadicadoModel;
use App\Models\DependenciaOrigenModel;
use App\Models\EvaluacionModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\RemisionQuejaModel;
use App\Models\TipoConductaProcesoDisciplinarioModel;
use App\Models\User;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class EvaluacionController extends Controller
{

    private $repository;
    use LogTrait;
    use RepartoAleatorioParametrizadoTrait;
    use PermisosExpedienteTrait;
    use NumeroCasosTrait;
    use ReclasificacionTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new EvaluacionModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = EvaluacionModel::query();
        $query = $query->select(
            'evaluacion.uuid',
            'evaluacion.id_proceso_disciplinario',
            'evaluacion.noticia_priorizada',
            'evaluacion.justificacion',
            'evaluacion.estado',
            'evaluacion.resultado_evaluacion',
            'evaluacion.tipo_conducta',
            'evaluacion.estado_evaluacion',
            'evaluacion.id_etapa',
            'evaluacion.eliminado',
        )->get();

        return EvaluacionCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EvaluacionFormRequest $request)
    {
        try {

            $datosRecibidos = $request->validated()["data"]["attributes"];

            $proceso_disciplinario = DB::select("select id_tipo_proceso from proceso_disciplinario where uuid = '" . $datosRecibidos["id_proceso_disciplinario"] . "'");

            $expediente = DB::select("select id_tipo_expediente, id_tipo_queja, id_tipo_derecho_peticion, id_termino_respuesta from clasificacion_radicado
            where id_proceso_disciplinario = '" . $datosRecibidos["id_proceso_disciplinario"] . "' and estado = 1");

            // se valida el tipo de expediente y se asigna el sub tipo de expediente
            if ($expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
                $sub_tipo_expediente = $expediente[0]->id_tipo_derecho_peticion;
            } elseif ($expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
                $sub_tipo_expediente = $expediente[0]->id_tipo_queja;
            } elseif ($expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja']) {
                $sub_tipo_expediente = $expediente[0]->id_tipo_queja;
            } elseif ($expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']) {
                if ($expediente[0]->fecha_termino != null) {
                    $sub_tipo_expediente = Constants::TIPO_TUTELA['dias'];
                } else {
                    $sub_tipo_expediente = Constants::TIPO_TUTELA['horas'];
                }
            } elseif ($expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {
                $sub_tipo_expediente = $expediente[0]->id_tipo_queja;
            }

            $tipo_reparto = DB::select("select id_tipo_cierre_etapa from cierre_etapa_configuracion
            where id_tipo_expediente = " . $expediente[0]->id_tipo_expediente . " and id_subtipo_expediente = " . $sub_tipo_expediente . " and id_tipo_proceso_disciplinario = " . $proceso_disciplinario[0]->id_tipo_proceso);

            if($datosRecibidos['reclasificacion']){
                error_log("RECLASIFICACION ENTRO");
                //Restablecer clasificación del radicado
                $queryClasificacionActual = ClasificacionRadicadoModel::where('id_proceso_disciplinario', $datosRecibidos["id_proceso_disciplinario"])
                    ->where('id_tipo_expediente', Constants::TIPO_EXPEDIENTE['proceso_disciplinario'])
                    ->whereNull('reclasificacion')
                    ->orderbyDesc('created_at')
                    ->get();

                $queryClasificacionAnterior = DB::select(
                    "
                        SELECT
                            ID_PROCESO_DISCIPLINARIO,
                            ID_ETAPA,
                            ID_TIPO_EXPEDIENTE,
                            OBSERVACIONES,
                            ID_TIPO_QUEJA,
                            ID_TERMINO_RESPUESTA,
                            FECHA_TERMINO,
                            HORA_TERMINO,
                            GESTION_JURIDICA,
                            ESTADO,
                            ID_ESTADO_REPARTO,
                            CREATED_USER,
                            OFICINA_CONTROL_INTERNO,
                            ID_TIPO_DERECHO_PETICION,
                            RECLASIFICACION,
                            ID_DEPENDENCIA,
                            VALIDACION_JEFE
                        FROM
                            CLASIFICACION_RADICADO
                        WHERE ID_PROCESO_DISCIPLINARIO = '".$datosRecibidos["id_proceso_disciplinario"]."'
                        AND ID_TIPO_EXPEDIENTE != ".Constants::TIPO_EXPEDIENTE['proceso_disciplinario']."
                        ORDER BY CREATED_AT DESC
                    "
                );

                if($queryClasificacionAnterior[0]->estado == Constants::ESTADOS['inactivo'] && count($queryClasificacionActual) > 0){ //SOLO FUNCIONA PARA QUEJA EXTERNA O INTERNA
                    $queryClasificacionAnterior = json_decode(json_encode($queryClasificacionAnterior), true);
                    $queryClasificacionAnterior[0]['reclasificacion'] = null;
                    $queryClasificacionAnterior[0]['estado'] = Constants::ESTADOS['activo'];
                    if(count($queryClasificacionActual) > 0){
                        ClasificacionRadicadoModel::where('uuid', $queryClasificacionActual[0]->uuid)->update(['reclasificacion' => 1, 'estado' => Constants::ESTADOS['inactivo']]);
                    }
                    ClasificacionRadicadoModel::create($queryClasificacionAnterior[0]);
                }
                $this->reclasificacionPorTipoEvaluacion($datosRecibidos['id_proceso_disciplinario']);
            }

            if ($proceso_disciplinario[0]->id_tipo_proceso == Constants::TIPO_DE_PROCESO['desglose']) {
                return $this->storeEvaluacionAsignadoAsiMismo($request);
            } else if ($tipo_reparto != null && $tipo_reparto[0]->id_tipo_cierre_etapa == Constants::TIPO_CIERRE_ETAPA['asignado_asi_mismo']) {
                return $this->storeEvaluacionAsignadoAsiMismo($request);
            } else {
                return $this->storeEvaluacionAprobacionJefe($request);
            }
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    /**
     * Store a newly created resource in storage.ADD
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeEvaluacionAprobacionJefe(EvaluacionFormRequest $request)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRecibidos = $request->validated()["data"]["attributes"];
            $datosRecibidos["id_etapa"] = $this->etapaActual($datosRecibidos["id_proceso_disciplinario"]);
            $datosRecibidos['eliminado'] = false;

            //buscamos el jefe de la dependencia
            $usuarioJefe = DependenciaOrigenModel::where("id", auth()->user()->id_dependencia)->first();

            if ($usuarioJefe->id_usuario_jefe) {

                //verificamos si el usuario esta activo para recibir casos
                $user = User::where("id", $usuarioJefe->id_usuario_jefe)->first();
                $dependencia = DependenciaOrigenModel::where("id", $user->id_dependencia)->first();

                //si el usuario jefe de la dependencia destino puede recibir expedientes continuamos
                if ($user->reparto_habilitado == true) {

                    //buscamos que expedientes puede realizar el usuario
                    $siRecibe = false;

                    // Valida si el usuario jefe de la dependencia donde se enviara el proceso está habilitado para recibir este tipo de expediente
                    $permisos = $this->validarPermisosExpedienteById($usuarioJefe->id_usuario_jefe, $datosRecibidos["id_proceso_disciplinario"]);

                    if ($permisos) {
                        $siRecibe = true;
                    } else {
                        $error['estado'] = false;
                        $error['error'] = 'El usuario destino ' . ($user->nombre . ' ' . $user->apellido . ' de la dependencia ' . $dependencia->nombre ) . ' no tiene tipos de expedientes asociados para recibir casos';
                        return json_encode($error);
                    }

                    if ($siRecibe) {

                        $datosRecibidos['created_user'] = auth()->user()->name;
                        $evaluacion = EvaluacionResource::make($this->repository->create($datosRecibidos));
                        $array = json_decode(json_encode($evaluacion));

                        // REGISTRAR TIPO DE CONDUCTA
                        $tipoConductaRequest['id_proceso_disciplinario'] = $datosRecibidos["id_proceso_disciplinario"];
                        $tipoConductaRequest['id_tipo_conducta'] = $datosRecibidos["tipo_conducta"];
                        $tipoConductaRequest['estado'] = true;
                        $tipoConductaRequest['id_etapa'] = LogTrait::etapaActual($datosRecibidos['id_proceso_disciplinario']);;
                        $tipoConductaRequest['created_user'] = auth()->user()->name;
                        $tipoConductaModel = new TipoConductaProcesoDisciplinarioModel();
                        TipoConductaProcesoDisciplinarioResource::make($tipoConductaModel->create($tipoConductaRequest));

                        // REGISTRAR EN EL LOG
                        $logRequest['id_proceso_disciplinario'] = $datosRecibidos["id_proceso_disciplinario"];
                        $logRequest['id_etapa'] = LogTrait::etapaActual($datosRecibidos['id_proceso_disciplinario']);
                        $logRequest['id_fase'] =  Constants::FASE['evaluacion'];
                        $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
                        $logRequest['descripcion'] = 'Remitido a través de evaluación al usuario jefe de la dependencia';
                        $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido']; // Remisionado
                        $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
                        $logRequest['id_funcionario_actual'] = $user->name;
                        $logRequest['id_funcionario_registra'] = auth()->user()->name;
                        $logRequest['id_funcionario_asignado'] = $user->name;
                        $logRequest['id_fase_registro'] = $array->id;

                        $this->numeroCasosUsuario($user->name);

                        $logModel = new LogProcesoDisciplinarioModel();
                        EvaluacionController::removerFuncionarioActualLog($datosRecibidos['id_proceso_disciplinario']);
                        LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
                    } else {
                        $error['estado'] = false;
                        $error['error'] = 'El usuario destino ' . ($user->nombre . ' ' . $user->apellido . ' de la dependencia ' . $dependencia->nombre ) . ' no puede recibir este caso, revise
                                        que expedientes tiene habilitado el usuario o que tipo de expedientes esta intentando reasignar';
                        return json_encode($error);
                    }
                } else {
                    $error['estado'] = false;
                    $error['error'] = 'El usuario destino ' . ($user->nombre . ' ' . $user->apellido . ' de la dependencia ' . $dependencia->nombre ) . ' no esta habilitado para recibir casos';
                    return json_encode($error);
                }
            } else {
                $error['estado'] = false;
                $error['error'] = 'La dependencia no tiene un usuario jefe asociado';
                return json_encode($error);
            }

            DB::connection()->commit();
            $respuesta['usuario_remitido'] = ($user->nombre . ' ' . $user->apellido . ' de la dependencia ' . $dependencia->nombre );
            return json_encode($respuesta);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeEvaluacionAsignadoAsiMismo(EvaluacionFormRequest $request)
    {
        try {


            DB::connection()->beginTransaction();
            $datosRecibidos = $request->validated()["data"]["attributes"];
            $datosRecibidos["id_etapa"] = $this->etapaActual($datosRecibidos["id_proceso_disciplinario"]);
            $datosRecibidos['eliminado'] = false;


            //verificamos si el usuario esta activo para recibir casos
            $user = User::where("id", auth()->user()->id)->first();
            $dependencia = DependenciaOrigenModel::where("id", $user->id_dependencia)->first();

            //si el usuario jefe de la dependencia destino puede recibir expedientes continuamos
            if ($user->reparto_habilitado == true) {

                $datosRecibidos['created_user'] = auth()->user()->name;

                /* CUANDO EL TIPO ES PODER PREFERENTE EL USUARIO QUE INICIO EL PROCESO LO FINALIZA.
                /SIN EMBARGO UNA EVALUACION ES APROBADA CUANDO EL ESTADO ES APROBADO. POR TAL MOTIVO ASIGNAMOS DIRECTAMENTE APROBADO*/
                $datosRecibidos['estado'] =  Constants::ESTADO_EVALUACION['aprobado_por_jefe'];

                $evaluacion = EvaluacionResource::make($this->repository->create($datosRecibidos));
                $array = json_decode(json_encode($evaluacion));

                // REGISTRAR TIPO DE CONDUCTA
                $tipoConductaRequest['id_proceso_disciplinario'] = $datosRecibidos["id_proceso_disciplinario"];
                $tipoConductaRequest['id_tipo_conducta'] = $datosRecibidos["tipo_conducta"];
                $tipoConductaRequest['estado'] = true;
                $tipoConductaRequest['id_etapa'] = LogTrait::etapaActual($datosRecibidos['id_proceso_disciplinario']);;
                $tipoConductaRequest['created_user'] = auth()->user()->name;
                $tipoConductaModel = new TipoConductaProcesoDisciplinarioModel();
                TipoConductaProcesoDisciplinarioResource::make($tipoConductaModel->create($tipoConductaRequest));

                // REGISTRAR EN EL LOG
                $logRequest['id_proceso_disciplinario'] = $datosRecibidos["id_proceso_disciplinario"];
                $logRequest['id_etapa'] = LogTrait::etapaActual($datosRecibidos['id_proceso_disciplinario']);
                $logRequest['id_fase'] =  Constants::FASE['evaluacion'];
                $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
                $logRequest['descripcion'] = 'Remitido a través de evaluación al usuario jefe de la dependencia';
                $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido']; // Remisionado
                $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
                $logRequest['id_funcionario_actual'] = auth()->user()->name;
                $logRequest['id_funcionario_registra'] = auth()->user()->name;
                $logRequest['id_funcionario_asignado'] = auth()->user()->name;
                $logRequest['id_fase_registro'] = $array->id;

                $logModel = new LogProcesoDisciplinarioModel();
                EvaluacionController::removerFuncionarioActualLog($datosRecibidos['id_proceso_disciplinario']);
                LogProcesoDisciplinarioResource::make($logModel->create($logRequest));


                //SI EL PROCESO ES UN DESGLOSE SE REGISTRA REMISION QUEJA
                $proceso_disciplinario = ProcesoDiciplinarioModel::where("uuid", $datosRecibidos["id_proceso_disciplinario"])->first();

                error_log($proceso_disciplinario);

                if ($proceso_disciplinario->id_tipo_proceso == Constants::TIPO_DE_PROCESO['desglose'] || $proceso_disciplinario->id_tipo_proceso == Constants::TIPO_DE_PROCESO['poder_preferente']) {

                    $remisionQueja['id_proceso_disciplinario'] = $datosRecibidos["id_proceso_disciplinario"];
                    $remisionQueja['id_tipo_evaluacion'] = $datosRecibidos['resultado_evaluacion'];
                    $remisionQueja['id_dependencia_origen'] = $proceso_disciplinario->id_dependencia;
                    $remisionQueja['id_dependencia_destino'] = $proceso_disciplinario->id_dependencia_duena;
                    $remisionQueja['eliminado'] = false;

                    $remisionModel = new RemisionQuejaModel();
                    $remision = RemisionQuejaResource::make($remisionModel->create($remisionQueja));
                    $array = json_decode(json_encode($remision));

                    // REGISTRAR EN EL LOG
                    $logRequest['id_proceso_disciplinario'] = $datosRecibidos["id_proceso_disciplinario"];
                    $logRequest['id_etapa'] = LogTrait::etapaActual($datosRecibidos['id_proceso_disciplinario']);
                    $logRequest['id_fase'] =  Constants::FASE['remision_queja'];
                    $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
                    $logRequest['descripcion'] = 'Se registra remision queja de manera automática';
                    $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido']; // Remisionado
                    $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
                    $logRequest['id_funcionario_actual'] = auth()->user()->name;
                    $logRequest['id_funcionario_registra'] = auth()->user()->name;
                    $logRequest['id_funcionario_asignado'] = auth()->user()->name;
                    $logRequest['id_fase_registro'] = $array->id;

                    $logModel = new LogProcesoDisciplinarioModel();
                    EvaluacionController::removerFuncionarioActualLog($datosRecibidos['id_proceso_disciplinario']);
                    LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
                }
            } else {
                $error['estado'] = false;
                $error['error'] = 'El usuario destino ' . ($user->nombre . ' ' . $user->apellido . ' de la dependencia ' . $dependencia->nombre) . ' no esta habilitado para recibir casos';
                return json_encode($error);
            }


            DB::connection()->commit();
            $respuesta['usuario_remitido'] = ($user->nombre . ' ' . $user->apellido . ' de la dependencia ' . $dependencia->nombre);
            return json_encode($respuesta);
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

    public function storeRemisionQuejaDesglose($datosRecibidos)
    {
        try {

            // REGISTRAR TIPO DE CONDUCTA
            $remision_queja['id_proceso_disciplinario'] = $datosRecibidos["id_proceso_disciplinario"];
            $tipoConductaRequest['id_tipo_conducta'] = $datosRecibidos["tipo_conducta"];
            $tipoConductaRequest['estado'] = true;
            $tipoConductaRequest['id_etapa'] = LogTrait::etapaActual($datosRecibidos['id_proceso_disciplinario']);;
            $tipoConductaRequest['created_user'] = auth()->user()->name;
            $tipoConductaModel = new TipoConductaProcesoDisciplinarioModel();
            TipoConductaProcesoDisciplinarioResource::make($tipoConductaModel->create($tipoConductaRequest));
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
     *
     */
    public function getAllEvalucionByIdProcesoDisciplinario($procesoDiciplinarioUUID)
    {
        // error_log($procesoDiciplinarioUUID);
        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID) {
            return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)->where('eliminado', false)->orderBy('created_at', 'desc')->get();
        });
        return EvaluacionCollection::make($query);
    }

    /**
     *
     */
    public function crearEvaluacionRemitida(EvaluacionFormRequest $request)
    {
        try {


            error_log("RECLASIFICACION P1");

            DB::connection()->beginTransaction();
            $datosRecibidos = $request->validated()["data"]["attributes"];
            // error_log(json_encode($datosRecibidos));
            //buscamos el usuario
            $evaluacion = EvaluacionModel::where("id_proceso_disciplinario", $datosRecibidos['id_proceso_disciplinario'])->where("estado_evaluacion", true)->where("eliminado", false)->first();

            $user = $this->repartoAleatorioParametrizado($datosRecibidos['id_proceso_disciplinario'], 0, 0, "", null, $evaluacion->created_user, 'E_EvaluacionAprobacion', true, $datosRecibidos['id_dependencia']);
            error_log("RECLASIFICACION P2");

            if (!$user->estado) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = $user->error;
                return $error;
            }

            error_log("RECLASIFICACION P3");

            if ($evaluacion && $user) {

                //Actualiza las actuales evaluaciones como inactivas.
                EvaluacionModel::where('id_proceso_disciplinario', $datosRecibidos['id_proceso_disciplinario'])->update(['estado_evaluacion' => 0]);

                // VALIDAMOS SI YA EXISTE UNA EVALUCION PREVIA

                $validacion_previa = DB::select("select uuid from evaluacion where id_proceso_disciplinario = '" . $datosRecibidos['id_proceso_disciplinario'] . "' and (estado = 2 or estado = 3) and eliminado = 0");

                if (count($validacion_previa) != 0) {
                    $this->reclasificacionPorTipoEvaluacion($datosRecibidos['id_proceso_disciplinario']);
                    $datosRecibidos['observaciones'] = "Reclasificacion por tipo de evaluacion";
                    $datosRecibidos['id_fase'] = Constants::FASE['ninguna'];
                }

                // Se registra la información en evaluacion
                $datosRecibidos['noticia_priorizada'] = $evaluacion["noticia_priorizada"];
                $datosRecibidos['resultado_evaluacion'] = ($datosRecibidos["resultado_evaluacion"] ? $datosRecibidos["resultado_evaluacion"] : $evaluacion["resultado_evaluacion"]);
                $datosRecibidos['tipo_conducta'] = $evaluacion["tipo_conducta"];
                $datosRecibidos['created_user'] = auth()->user()->name;
                $datosRecibidos['estado_evaluacion'] = true;
                $datosRecibidos["id_etapa"] = $this->etapaActual($datosRecibidos["id_proceso_disciplinario"]);
                $datosRecibidos["eliminado"] = false;

                $query = EvaluacionResource::make($this->repository->create($datosRecibidos));
                $array = json_decode(json_encode($query));

                $logModel = new LogProcesoDisciplinarioModel();
                $logRequest['id_proceso_disciplinario'] = $evaluacion["id_proceso_disciplinario"];
                $logRequest['id_etapa'] = Constants::ETAPA['evaluacion'];
                $logRequest['id_fase'] = Constants::FASE['evaluacion'];
                $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa'];
                $logRequest['descripcion'] =  $datosRecibidos['justificacion'];
                $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
                $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
                $logRequest['id_funcionario_actual'] = $user->id_funcionario_actual;
                $logRequest['id_funcionario_asignado'] = $user->id_funcionario_actual;
                $logRequest['id_funcionario_registra'] = auth()->user()->name;
                $logRequest['id_fase_registro'] = $array->id;
                EvaluacionController::removerFuncionarioActualLog($datosRecibidos['id_proceso_disciplinario']);

                LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

                $this->numeroCasosUsuario($user->id_funcionario_actual);
            }

            error_log("RECLASIFICACION P4");


            $dependencia = DependenciaOrigenModel::where("id", $user->id_dependencia_origen)->first();
            $respuesta['usuario_remitido'] = ($user->nombre_completo . ' ' . $user->apellido_completo . ' de la dependencia ' . $dependencia->nombre);
            DB::connection()->commit();

            return json_encode($respuesta);
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
        return EvaluacionResource::make($this->repository->find($id));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EvaluacionFormRequest $request, $id)
    {
        //
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
     * Update Tipo de conducta
     *
     */
    public function updateTipoConducta(EvaluacionFormRequest $request)
    {

        $datosRequest = $request->validated()["data"]["attributes"];
        $evaluacion = EvaluacionModel::where("id_proceso_disciplinario", $datosRequest['id_proceso_disciplinario'])->where('estado_evaluacion', Constants::ESTADOS['activo'])->get();

        if ($evaluacion != null) {

            // SE ACTUALIZA LOS ESTADOS DEL ACTUAL RESGITRO ACTIVO DE EVALUACION
            $datosRequest['noticia_priorizada'] = $evaluacion[0]->noticia_priorizada;
            $datosRequest['justificacion'] = $evaluacion[0]->justificacion;
            $datosRequest['resultado_evaluacion'] = $evaluacion[0]->resultado_evaluacion;
            $datosRequest['created_user'] = auth()->user()->name;
            $datosRequest['estado_evaluacion'] = true;
            $datosRequest["id_etapa"] = $evaluacion[0]->id_etapa;

            $query = EvaluacionResource::make($this->repository->create($datosRequest));
            $array = json_decode(json_encode($query));

            EvaluacionModel::where("uuid", $evaluacion[0]->uuid)->update(['estado_evaluacion' => Constants::ESTADOS['inactivo']]);

            // TRAER EL ULTIMO LOG REGSITRADO DEL PROCESO DISCIPLINARIO
            $ultimo_log = LogProcesoDisciplinarioModel::where("id_proceso_disciplinario", $datosRequest['id_proceso_disciplinario'])->where('id_funcionario_actual', '<>', null)->get();

            $logModel = new LogProcesoDisciplinarioModel();
            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = $ultimo_log[0]->id_etapa;
            $logRequest['id_fase'] = $ultimo_log[0]->id_fase;
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa'];
            $logRequest['descripcion'] =  "Se actualizo el tipo de conducta";
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['id_funcionario_actual'] = $evaluacion[0]->created_user;
            $logRequest['id_funcionario_asignado'] = $evaluacion[0]->created_user;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_fase_registro'] = $array->id;
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->orderBy('created_at', 'desc')->get();
            });

            return EvaluacionCollection::make($query);
        }
    }

    /**
     * OBTENER TIPO DE PROCESO DISCIPLINARIO
     */
    public function getEstadoEvaluacion($id_proceso_disciplinario)
    {

        // Se valida la dependencia en la que se encuentra el usuario en sesión
        $evaluacion_aprobado = EvaluacionModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)->where('estado', Constants::ESTADO_EVALUACION['aprobado_por_jefe'])->where('eliminado', false)->get();
        $evaluacion_rechazado = EvaluacionModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)->where('estado', Constants::ESTADO_EVALUACION['rechazado_por_jefe'])->where('eliminado', false)->get();
        $evaluacion_recibido = EvaluacionModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)->where('estado', Constants::ESTADO_EVALUACION['registrado'])->where('eliminado', false)->get();

        if (count($evaluacion_aprobado) > 0) {
            // error_log("evaluacion_aprobado");
            $reciboDatos['estado_evaluacion'] = $evaluacion_aprobado[0]->estado;
            $json['data']['attributes'] = $reciboDatos;
            return json_encode($json);
        }

        if (count($evaluacion_rechazado) > 0) {
            // error_log("evaluacion_rechazado");
            $reciboDatos['estado_evaluacion'] = $evaluacion_rechazado[0]->estado;
            $json['data']['attributes'] = $reciboDatos;
            return json_encode($json);
        }

        if (count($evaluacion_recibido) > 0) {
            // error_log("evaluacion_recibido");
            $reciboDatos['estado_evaluacion'] = $evaluacion_recibido[0]->estado;
            $json['data']['attributes'] = $reciboDatos;
            return json_encode($json);
        }

        $reciboDatos['estado_evaluacion'] = 0;
        $json['data']['attributes'] = $reciboDatos;
        return json_encode($json);
    }
}
