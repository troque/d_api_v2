<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AsignacionProcesoDisciplinarioTrait;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Controllers\Traits\MailTrait;
use App\Http\Controllers\Traits\NumeroCasosTrait;
use App\Http\Controllers\Traits\ReclasificacionTrait;
use App\Http\Controllers\Traits\RepartoAleatorioTrait;
use App\Http\Requests\CierreEtapaFormRequest;
use App\Http\Resources\CierreEtapa\CierreEtapaCollection;
use App\Http\Resources\CierreEtapa\CierreEtapaResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Resources\ValidarClasificacion\ValidarClasificacionResource;
use App\Http\Utilidades\Constants;
use App\Models\ActuacionesModel;
use App\Models\CierreEtapaModel;
use App\Models\ClasificacionRadicadoModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\TrazabilidadActuacionesModel;
use App\Models\ValidarClasificacionModel;
use App\Repositories\RepositoryGeneric;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class CierreEtapaController extends Controller
{
    private $repository;
    use AsignacionProcesoDisciplinarioTrait;
    use LogTrait;
    use RepartoAleatorioTrait;
    use MailTrait;
    use NumeroCasosTrait;
    use ReclasificacionTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new CierreEtapaModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return CierreEtapaCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * CIERRE DE ETAPA DE CAPTURA Y REPARTO
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CierreEtapaFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        //$datosRequest['eliminado'] = false;

        // SE VERIFICA TIPO DE PROCESO DISCIPLINARIO, TIPO DE EXPEDIENTE, SUB_TIPO_EXPEDIENTE Y ETAPA ACTUAL DEL PROCESO DISCIPLINARIO
        $expediente = DB::select("
            SELECT
                proceso_disciplinario.id_tipo_proceso as id_tipo_proceso,
                proceso_disciplinario.id_etapa as id_etapa,
                clasificacion_radicado.id_tipo_expediente as id_tipo_expediente,
                clasificacion_radicado.id_tipo_derecho_peticion as id_tipo_derecho_peticion,
                clasificacion_radicado.id_tipo_queja as id_tipo_queja,
                clasificacion_radicado.id_termino_respuesta as id_termino_respuesta
            FROM
                proceso_disciplinario
            INNER JOIN clasificacion_radicado ON clasificacion_radicado.id_proceso_disciplinario = proceso_disciplinario.uuid
            WHERE proceso_disciplinario.uuid = '" . $datosRequest['id_proceso_disciplinario'] . "'
            AND clasificacion_radicado.estado = 1
        ");

        if ($expediente[0]->id_tipo_derecho_peticion != null) {
            $sub_tipo_expediente = $expediente[0]->id_tipo_derecho_peticion;
        } elseif ($expediente[0]->id_tipo_queja != null) {
            $sub_tipo_expediente = $expediente[0]->id_tipo_queja;
        } elseif ($expediente[0]->id_termino_respuesta != null) {
            $sub_tipo_expediente = $expediente[0]->id_termino_respuesta;
        }


        $tipo_cierre = DB::select(
            "
            SELECT
                id_tipo_cierre_etapa
            FROM
                cierre_etapa_configuracion
            WHERE id_tipo_proceso_disciplinario = " . $expediente[0]->id_tipo_proceso . "
            AND id_etapa = " . $expediente[0]->id_etapa . "
            AND id_tipo_expediente = " . $expediente[0]->id_tipo_expediente . "
            AND id_subtipo_expediente = " . $sub_tipo_expediente
        );

        /*$aux =  "SELECT
            id_tipo_cierre_etapa
        FROM
            cierre_etapa_configuracion
        WHERE id_tipo_proceso_disciplinario = " . $expediente[0]->id_tipo_proceso . "
        AND id_etapa = " . $expediente[0]->id_etapa . "
        AND id_tipo_expediente = " . $expediente[0]->id_tipo_expediente . "
        AND id_subtipo_expediente = " . $sub_tipo_expediente;

        error_log($aux);*/


        $proceso_disciplinario =  DB::select("select id_dependencia_actual from proceso_disciplinario where uuid = '" . $datosRequest['id_proceso_disciplinario'] . "'");
        $datosRequest['id_dependencia_actual'] = $proceso_disciplinario[0]->id_dependencia_actual;

        if ($datosRequest['id_etapa'] == Constants::ETAPA['captura_reparto'] && $tipo_cierre[0]->id_tipo_cierre_etapa == Constants::TIPO_CIERRE_ETAPA['reparto_aleatorio']) {
            error_log("CIERRE DE ETAPA CAPTURA Y REPARTO CIERRE ALEATORIO");
            return $this->cierreCapturaRepartoRepartoAleatorio($datosRequest);
        } else if ($datosRequest['id_etapa'] == Constants::ETAPA['captura_reparto'] && $tipo_cierre[0]->id_tipo_cierre_etapa == Constants::TIPO_CIERRE_ETAPA['asignado_asi_mismo']) {
            error_log("CIERRE DE ETAPA CAPTURA ASIGNADO ASI MISMO");
            return $this->cierreCapturaRepartoAsignadoAsiMismo($datosRequest, $sub_tipo_expediente);
        } elseif ($datosRequest['id_etapa'] == Constants::ETAPA['evaluacion']) {

            $aux = "SELECT resultado_evaluacion FROM evaluacion WHERE id_proceso_disciplinario = '" . $datosRequest['id_proceso_disciplinario'] . "' AND eliminado = false";
            // error_log($aux);

            $tipo_evaluacion = DB::select("SELECT resultado_evaluacion FROM evaluacion WHERE id_proceso_disciplinario = '" . $datosRequest['id_proceso_disciplinario'] . "' AND estado_evaluacion = 1 AND eliminado = 0");

            // HAY CIERRE DEFINITVO CUANDO EL TIPO DE EVALUACION EN DEVOLUCION ENTIDAD
            if ($tipo_evaluacion != null && $tipo_evaluacion[0]->resultado_evaluacion == Constants::RESULTADO_EVALUACION['devolucion_entidad']) {
                return $this->cierreEvaluacionArchivado($datosRequest);
            } else if ($tipo_evaluacion != null && $tipo_evaluacion[0]->resultado_evaluacion == Constants::RESULTADO_EVALUACION['remisorio_externo']) {
                return $this->cierreEvaluacionArchivado($datosRequest);
            } else {
                if ($tipo_cierre[0]->id_tipo_cierre_etapa == Constants::TIPO_CIERRE_ETAPA['asignacion_dirigida']) {
                    error_log("EVALUACION ASIGNACION DIRIGIDA");
                    return $this->cierreEtapaEvaluacionAsignacionDirigida($datosRequest, $expediente[0]->id_tipo_expediente, $sub_tipo_expediente);
                } else if ($tipo_cierre[0]->id_tipo_cierre_etapa == Constants::TIPO_CIERRE_ETAPA['asignado_asi_mismo']) {
                    error_log(" EVALUACION ASIGNADO A SI MISMO");
                    if ($expediente[0]->id_tipo_queja == Constants::TIPO_QUEJA['interna']) {
                        $datosRequest['queja_interna'] = true;
                    }
                    return $this->cierreEtapaEvaluacionAsiMismo($datosRequest);
                } else if ($tipo_cierre[0]->id_tipo_cierre_etapa == Constants::TIPO_CIERRE_ETAPA['cierre_definitivo']) {
                    return $this->cierreEvaluacionArchivado($datosRequest);
                }
            }
        }
    }

    /**
     *
     */
    public function cierreCapturaRepartoRepartoAleatorio($datosRequest)
    {

        // SE VERIFICA TIPO DE PROCESO DISCIPLINARIO, TIPO DE EXPEDIENTE, SUB_TIPO_EXPEDIENTE Y ETAPA ACTUAL DEL PROCESO DISCIPLINARIO
        $jefe_dependiencia = DB::select("select id_usuario_jefe from mas_dependencia_origen where id = " . auth()->user()->id_dependencia);

        try {
            // SE VALIDA QUE LA DEPENDENCIA TENGA UN JEFE ASIGNADO
            $jefe_dependiencia = DB::select("select id_usuario_jefe from mas_dependencia_origen where id = " . auth()->user()->id_dependencia);
            //  error_log("JEFE DE DEPENDENCIA: ".$jefe_dependiencia[0]->id_usuario_jefe);

            if (empty($jefe_dependiencia[0]->id_usuario_jefe)) {

                $error['estado'] = false;
                $error['error'] = 'LA DEPENDENCIA NO TIENE JEFE ASIGNADO.';

                return json_encode($error);
            }

            DB::connection()->beginTransaction();

            // REPARTO Y BALANCEO DE CARGA
            $funcionario_asignado = CierreEtapaController::storeRepartoAleatorio(auth()->user()->name, $datosRequest['id_proceso_disciplinario'], auth()->user()->id_dependencia);

            if ($funcionario_asignado != null) {
                $funcionario_asignado_name = $funcionario_asignado->nombre_funcionario;
            } else {
                $error['estado'] = false;
                $error['error'] = 'NO EXISTEN FUNCIONARIOS QUE TENGAS PERMISOS PARA ATENDER ESTE TIPO DE EXPEDIENTE.';
                return json_encode($error);
            }

            // ACTUALIZA ETAPA ACTUAL DE PROCESO DISCIPLINARIO
            ProcesoDiciplinarioModel::where('uuid', $datosRequest['id_proceso_disciplinario'])->update(['id_etapa' => $datosRequest['id_etapa']]);

            // SE CIERRA LA ETAPA
            $datosRequest['id_funcionario_asignado'] = $funcionario_asignado->nombre_funcionario;
            $datosRequest['eliminado'] = false;
            $respuesta = CierreEtapaResource::make($this->repository->create($datosRequest));

            $array = json_decode(json_encode($respuesta));

            // LOG PROCESO DISCIPLINARIO
            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null, 'id_estado' => Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['contestado']]);

            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] =  LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['descripcion'] = "Cierre de etapa captura y reparto";
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['id_fase'] = Constants::FASE['cierre_captura_reparto'];
            $logRequest['id_funcionario_actual'] = $funcionario_asignado_name;
            $logRequest['id_funcionario_asignado'] = $funcionario_asignado_name;
            $logRequest['id_funcionario_registra'] =  $datosRequest['created_user'];
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['cierre_etapa'];
            $logRequest['id_fase_registro'] = $array->id;

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            // SE ASIGNA RECLASIFICACION = TRUE AL EXPEDIENTE QUE QUEDO SELECCIONADO EN EL MOMENTO DEL CIERRE DE LA ETAPA
            ClasificacionRadicadoModel::where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->where('estado', true)->update(['reclasificacion' => true]);

            // SE ACTUALIZA LA ETAPA EN EL PROCESO DISCIPLINARIO
            ProcesoDiciplinarioModel::where('uuid', $datosRequest['id_proceso_disciplinario'])->update(['id_etapa' => Constants::ETAPA['evaluacion']]);

            // SE VALIDA CLASIFICACION DEL RADICADO
            error_log("REPARTO ALEATORIO");
            //$this->validarClasificado($datosRequest);

            //ENVIAR EMAIL. SE TRAES LOS DATOS DEL USUARIO AIGNADO Y EL RADICADO DEL PROCESO DISCIPLINARIO
            $usuario_asignado = DB::select("select nombre, apellido, email from users where name = '" . $funcionario_asignado_name . "'");
            $proceso = DB::select("select radicado from proceso_disciplinario where uuid = '" . $datosRequest['id_proceso_disciplinario'] . "'");

            if (!empty($respuesta)) {
                try {
                    $this->sendMail(
                        $usuario_asignado[0]->email,
                        $usuario_asignado[0]->nombre . " " . $usuario_asignado[0]->apellido,
                        'Se remite proceso ' . $proceso[0]->radicado . ' para gestión',
                        'Se finaliza la etapa de captura y reparto. Se asigna el proceso disciplinario ' . $proceso[0]->radicado . ' a su lista de pendientes',
                        null,
                        null,
                        null,
                    );
                } catch (ErrorException $e) {
                    error_log($e);
                }
            }

            DB::connection()->commit();
            // error_log(json_encode($respuesta));

            return $respuesta;
        } catch (\Exception $e) {
            error_log($e);
        }
    }

    /**
     *
     */
    public function cierreCapturaRepartoAsignadoAsiMismo($datosRequest)
    {

        // VERIFICA QUE LA DEPENDENCIA TENGA JEFE
        $jefe_dependiencia = DB::select("select id_usuario_jefe from mas_dependencia_origen where id = " . auth()->user()->id_dependencia);

        try {
            // SE VALIDA QUE LA DEPENDENCIA TENGA UN JEFE ASIGNADO
            $jefe_dependiencia = DB::select("select id_usuario_jefe from mas_dependencia_origen where id = " . auth()->user()->id_dependencia);


            error_log("PRUEBA....");

            if (empty($jefe_dependiencia[0]->id_usuario_jefe)) {

                $error['estado'] = false;
                $error['error'] = 'LA DEPENDENCIA NO TIENE JEFE ASIGNADO.';

                return json_encode($error);
            }

            DB::connection()->beginTransaction();


            $funcionario_asignado_id = auth()->user()->id;
            $funcionario_asignado_name = auth()->user()->name;

            // ACTUALIZA ETAPA ACTUAL DE PROCESO DISCIPLINARIO
            ProcesoDiciplinarioModel::where('uuid', $datosRequest['id_proceso_disciplinario'])->update(['id_etapa' => $datosRequest['id_etapa']]);

            // SE CIERRA LA ETAPA
            $datosRequest['id_funcionario_asignado'] = $funcionario_asignado_name;
            $datosRequest['eliminado'] = false;
            $respuesta = CierreEtapaResource::make($this->repository->create($datosRequest));

            $array = json_decode(json_encode($respuesta));

            // LOG PROCESO DISCIPLINARIO
            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null, 'id_estado' => Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['contestado']]);

            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] =  LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['descripcion'] = "Cierre de etapa captura y reparto";
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['id_fase'] = Constants::FASE['cierre_captura_reparto'];
            $logRequest['id_funcionario_actual'] = $funcionario_asignado_name;
            $logRequest['id_funcionario_asignado'] = $funcionario_asignado_name;
            $logRequest['id_funcionario_registra'] =  $datosRequest['created_user'];
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['cierre_etapa'];
            $logRequest['id_fase_registro'] = $array->id;

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            // SE ASIGNA RECLASIFICACION = TRUE AL EXPEDIENTE QUE QUEDO SELECCIONADO EN EL MOMENTO DEL CIERRE DE LA ETAPA
            ClasificacionRadicadoModel::where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->where('estado', true)->update(['reclasificacion' => true]);

            // SE ACTUALIZA LA ETAPA EN EL PROCESO DISCIPLINARIO
            ProcesoDiciplinarioModel::where('uuid', $datosRequest['id_proceso_disciplinario'])->update(['id_etapa' => Constants::ETAPA['evaluacion']]);

            // SE VALIDA CLASIFICACION DEL RADICADO
            error_log("REPARTO ASI MISMO");
            $this->validarClasificado($datosRequest);

            //ENVIAR EMAIL. SE TRAES LOS DATOS DEL USUARIO AIGNADO Y EL RADICADO DEL PROCESO DISCIPLINARIO
            $usuario_asignado = DB::select("select nombre, apellido, email from users where name = '" . $funcionario_asignado_name . "'");
            $proceso = DB::select("select radicado from proceso_disciplinario where uuid = '" . $datosRequest['id_proceso_disciplinario'] . "'");

            if (!empty($respuesta)) {
                try {
                    $this->sendMail(
                        $usuario_asignado[0]->email,
                        $usuario_asignado[0]->nombre . " " . $usuario_asignado[0]->apellido,
                        'Se remite proceso ' . $proceso[0]->radicado . ' para gestión',
                        'Se finaliza la etapa de captura y reparto. Se asigna el proceso disciplinario ' . $proceso[0]->radicado . ' a su lista de pendientes',
                        null,
                        null,
                        null,
                    );
                } catch (ErrorException $e) {
                    error_log($e);
                }
            }

            DB::connection()->commit();

            return $respuesta;
        } catch (\Exception $e) {
        }
    }

    /**
     *
     */
    public function cierreEtapaEvaluacionAsignacionDirigida($datosRequest, $tipo_expediente, $sub_tipo_expediente)
    {

        $tipo_evaluacion =  DB::select("select resultado_evaluacion from evaluacion where id_proceso_disciplinario = '" . $datosRequest['id_proceso_disciplinario'] . "'");

        if (
            $tipo_evaluacion != null && ($tipo_evaluacion[0]->resultado_evaluacion == Constants::RESULTADO_EVALUACION['incorporacion'] ||
                $tipo_evaluacion[0]->resultado_evaluacion == Constants::RESULTADO_EVALUACION['comisorio_eje'] ||
                $tipo_evaluacion[0]->resultado_evaluacion == Constants::RESULTADO_EVALUACION['remisorio_interno']
            )
        ) {

            if ($tipo_expediente == Constants::TIPO_EXPEDIENTE['queja'] && $sub_tipo_expediente == Constants::TIPO_QUEJA['interna']) {

                $funcionario_asignado = DB::select("
                SELECT
                    users.name AS nombre_funcionario,
                    users.id AS id_funcionario_asignado,
                    users.numero_casos AS num_casos
                FROM
                    users
                INNER JOIN mas_dependencia_origen ON users.id = mas_dependencia_origen.id_usuario_jefe
                WHERE mas_dependencia_origen.id = " . auth()->user()->id_dependencia);

                error_log("queja interna");

                $datosRequest['id_funcionario_asignado'] = $funcionario_asignado[0]->id_funcionario_asignado;
            } else {

                $funcionario_asignado = DB::select("
                SELECT
                    users.name AS nombre_funcionario,
                    users.id AS id_funcionario_asignado,
                    users.numero_casos AS num_casos
                FROM
                    remision_queja
                INNER JOIN mas_dependencia_origen ON mas_dependencia_origen.id = remision_queja.id_dependencia_destino
                INNER JOIN users ON users.id = mas_dependencia_origen.id_usuario_jefe
                WHERE remision_queja.id_proceso_disciplinario ='" . $datosRequest['id_proceso_disciplinario'] . "'");

                if ($funcionario_asignado != null) {
                    $datosRequest['id_funcionario_asignado'] = $funcionario_asignado[0]->id_funcionario_asignado;
                } else {
                    $datosRequest['id_funcionario_asignado'] = null;
                }
            }
        } else if ($tipo_evaluacion != null && $tipo_evaluacion[0]->resultado_evaluacion == Constants::RESULTADO_EVALUACION['devolucion_entidad']) {

            $funcionario_asignado = DB::select("
                SELECT
                    users.name AS nombre_funcionario,
                    users.id AS id_funcionario_asignado,
                    users.numero_casos AS num_casos
                FROM
                    users
                INNER JOIN mas_dependencia_origen ON users.id = mas_dependencia_origen.id_usuario_jefe
                WHERE mas_dependencia_origen.id = " . auth()->user()->id_dependencia);

            if ($funcionario_asignado != null) {
                $datosRequest['id_funcionario_asignado'] = $funcionario_asignado[0]->id_funcionario_asignado;
                $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
                $logRequest['id_funcionario_actual'] = $funcionario_asignado[0]->nombre_funcionario;
            } else {
                $datosRequest['id_funcionario_asignado'] = null;
            }
        } else if ($tipo_evaluacion != null && $tipo_evaluacion[0]->resultado_evaluacion == Constants::RESULTADO_EVALUACION['remisorio_externo']) {
            $datosRequest['id_funcionario_asignado'] = null;
        }


        try {
            DB::connection()->beginTransaction();

            if ($datosRequest['id_funcionario_asignado'] != null) {

                $datosRequest['id_funcionario_asignado'] = $funcionario_asignado[0]->nombre_funcionario;
                $datosRequest['eliminado'] = false;

                $respuesta = CierreEtapaResource::make($this->repository->create($datosRequest));
                $array = json_decode(json_encode($respuesta));

                // LOG PROCESO DISCIPLINARIO
                LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null, 'id_estado' => Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['contestado']]);
                // error_log($funcionario_asignado[0]->id_funcionario);

                $this->reclasificacionProcesoDisciplinario($datosRequest['id_proceso_disciplinario']);

                $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
                $logRequest['id_etapa'] =  $datosRequest['id_etapa'];
                $logRequest['id_fase'] = Constants::FASE['cierre_evaluacion'];
                $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
                $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
                $logRequest['descripcion'] = "cierre de evaluación";
                $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
                $logRequest['id_funcionario_actual'] = $funcionario_asignado[0]->nombre_funcionario;
                $logRequest['id_funcionario_asignado'] = $funcionario_asignado[0]->nombre_funcionario;
                $logRequest['id_funcionario_registra'] =  $datosRequest['created_user'];
                $logRequest['id_tipo_expediente'] = "";
                $logRequest['id_tipo_sub_expediente'] = "";
                $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['cierre_etapa'];
                $logRequest['id_fase_registro'] = $array->id;


                $logModel = new LogProcesoDisciplinarioModel();
                LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

                $this->numeroCasosUsuario($funcionario_asignado[0]->nombre_funcionario);


                // SE ACTUALIZA LA ETAPA EN EL PROCESO DISCIPLINARIO
                $poder_preferente = DB::select("select id_etapa_asignada from proceso_poder_preferente where uuid = '" . $datosRequest['id_proceso_disciplinario'] . "'");

                if (!empty($poder_preferente)) {
                    ProcesoDiciplinarioModel::where('uuid', $datosRequest['id_proceso_disciplinario'])->update(['id_etapa' => $poder_preferente[0]->id_etapa_asignada, 'id_dependencia_actual', $datosRequest['id_dependencia_actual']);
                } else {
                    ProcesoDiciplinarioModel::where('uuid', $datosRequest['id_proceso_disciplinario'])->update(['id_etapa' => Constants::ETAPA['evaluacion_pd'], 'id_dependencia_actual', $proceso_disciplinario[0]->id_dependencia_actual]);
                }


                //ENVIAR EMAIL. SE TRAES LOS DATOS DEL USUARIO ASIGNADO Y EL RADICADO DEL PROCESO DISCIPLINARIO
                $usuario_asignado = DB::select("select nombre, apellido, email from users where name = '" . $funcionario_asignado[0]->nombre_funcionario . "'");
                $proceso = DB::select("select radicado from proceso_disciplinario where uuid = '" . $datosRequest['id_proceso_disciplinario'] . "'");

                if (!empty($respuesta)) {
                    try {
                        $this->sendMail(
                            $usuario_asignado[0]->email,
                            $usuario_asignado[0]->nombre . " " . $usuario_asignado[0]->apellido,
                            'Se remite proceso ' . $proceso[0]->radicado . ' para gestión',
                            'Se finaliza la etapa de evaluación. Se asigna el proceso disciplinario ' . $proceso[0]->radicado . ' a su lista de pendientes',
                            null,
                            null,
                            null,
                        );
                    } catch (ErrorException $e) {
                        error_log($e);
                    }
                }

                DB::connection()->commit();
                return $respuesta;
            } else {

                $error['estado'] = false;
                $error['error'] = 'La dependencia no tiene jefe asignado.';

                return json_encode($error);

                DB::connection()->beginTransaction();
            }
        } catch (\Exception $e) {
            error_log($e);

            DB::connection()->rollBack();

            if (empty($results)) {

                $error['estado'] = false;
                $error['error'] = 'No existen funcionarios con permisos para gestionar este tipo de expediente o no están habilitados para reparto.';

                return json_encode($error);
            }
        }
    }

    /**
     *
     */
    public function cierreEtapaEvaluacionAsiMismo($datosRequest)
    {

        try {

            DB::connection()->beginTransaction();

            $datosRequest['id_funcionario_asignado'] =  auth()->user()->name;
            $datosRequest['eliminado'] = false;
            $respuesta = CierreEtapaResource::make($this->repository->create($datosRequest));
            $array = json_decode(json_encode($respuesta));

            // LOG PROCESO DISCIPLINARIO
            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null, 'id_estado' => Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['contestado']]);
            // error_log($funcionario_asignado[0]->id_funcionario);

            $this->reclasificacionProcesoDisciplinario($datosRequest['id_proceso_disciplinario']);

            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] =  $datosRequest['id_etapa'];
            $logRequest['id_fase'] = Constants::FASE['cierre_evaluacion'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['descripcion'] = "cierre de evaluación";
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['id_funcionario_actual'] =  auth()->user()->name;
            $logRequest['id_funcionario_asignado'] =  auth()->user()->name;
            $logRequest['id_funcionario_registra'] =  $datosRequest['created_user'];
            $logRequest['id_tipo_expediente'] = "";
            $logRequest['id_tipo_sub_expediente'] = "";
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['cierre_etapa'];
            $logRequest['id_fase_registro'] = $array->id;


            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            // SE ACTUALIZA LA ETAPA EN EL PROCESO DISCIPLINARIO
            $poder_preferente = DB::select("select id_etapa_asignada from proceso_poder_preferente where uuid = '" . $datosRequest['id_proceso_disciplinario'] . "'");

            if (!empty($poder_preferente)) {
                ProcesoDiciplinarioModel::where('uuid', $datosRequest['id_proceso_disciplinario'])->update(['id_etapa' => $poder_preferente[0]->id_etapa_asignada]);
            } else {
                ProcesoDiciplinarioModel::where('uuid', $datosRequest['id_proceso_disciplinario'])->update(['id_etapa' => Constants::ETAPA['evaluacion_pd']]);
            }

            //SI ES UNA QUEJA INTERNA
            if (isset($datosRequest['queja_interna'])) {
                if ($datosRequest['queja_interna']) {
                    $respuesta = ProcesoDiciplinarioModel::where('uuid', $datosRequest['id_proceso_disciplinario'])
                        ->update(['usuario_comisionado' => auth()->user()->id]);

                    $estado_registro = $this->autoRegistrarActuacion($datosRequest['id_proceso_disciplinario']);

                    if ($estado_registro && $estado_registro->estado == false) {
                        return $estado_registro;
                    }
                }
            }

            //dd("Fin");

            //ENVIAR EMAIL. SE TRAES LOS DATOS DEL USUARIO ASIGNADO Y EL RADICADO DEL PROCESO DISCIPLINARIO
            $usuario_asignado = DB::select("select nombre, apellido, email from users where name = '" . auth()->user()->name . "'");
            $proceso = DB::select("select radicado from proceso_disciplinario where uuid = '" . $datosRequest['id_proceso_disciplinario'] . "'");

            if (!empty($respuesta)) {
                try {
                    $this->sendMail(
                        $usuario_asignado[0]->email,
                        $usuario_asignado[0]->nombre . " " . $usuario_asignado[0]->apellido,
                        'Se remite proceso ' . $proceso[0]->radicado . ' para gestión',
                        'Se finaliza la etapa de evaluación. Se asigna el proceso disciplinario ' . $proceso[0]->radicado . ' a su lista de pendientes',
                        null,
                        null,
                        null,
                    );
                } catch (ErrorException $e) {
                    error_log($e);
                }
            }

            DB::connection()->commit();
            return $respuesta;
        } catch (\Exception $e) {
            error_log($e);

            DB::connection()->rollBack();

            if (empty($results)) {

                $error['estado'] = false;
                $error['error'] = 'No existen funcionarios con permisos para gestionar este tipo de expediente o no están habilitados para reparto.';

                return json_encode($error);
            }
        }
    }

    /**
     *
     */
    public function cierreEvaluacionArchivado($datosRequest)
    {


        DB::connection()->beginTransaction();

        // SE CIERRA LA ETAPA
        $datosRequest['id_funcionario_asignado'] =  null;
        $datosRequest['eliminado'] = false;
        $respuesta = CierreEtapaResource::make($this->repository->create($datosRequest));

        LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

        $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
        $logRequest['id_etapa'] =  $datosRequest['id_etapa'];
        $logRequest['id_fase'] = Constants::FASE['cierre_evaluacion'];
        $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
        $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['finalizado'];
        $logRequest['descripcion'] = "cierre de evaluación";
        $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
        $logRequest['id_funcionario_actual'] = null;
        $logRequest['id_funcionario_asignado'] = null;
        $logRequest['id_funcionario_registra'] = $datosRequest['id_funcionario_asignado'];
        $logRequest['id_tipo_expediente'] = "";
        $logRequest['id_tipo_sub_expediente'] = "";
        $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['cierre_etapa'];
        $logRequest['id_fase_registro'] = null;

        $logModel = new LogProcesoDisciplinarioModel();
        LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

        ProcesoDiciplinarioModel::where('uuid', $datosRequest['id_proceso_disciplinario'])->update(['estado' => 2]);

        //ENVIAR EMAIL. SE TRAES LOS DATOS DEL USUARIO ASIGNADO Y EL RADICADO DEL PROCESO DISCIPLINARIO
        $usuario_asignado = DB::select("select nombre, apellido, email from users where name = '" . $datosRequest['id_funcionario_asignado'] . "'");
        $proceso = DB::select("select radicado from proceso_disciplinario where uuid = '" . $datosRequest['id_proceso_disciplinario'] . "'");

        if (!empty($respuesta)) {
            try {
                $this->sendMail(
                    $usuario_asignado[0]->email,
                    $usuario_asignado[0]->nombre . " " . $usuario_asignado[0]->apellido,
                    'El proceso ' . $proceso[0]->radicado . ' ha sido archivado',
                    'El proceso ' . $proceso[0]->radicado . ' ha sido archivado',
                    null,
                    null,
                    null,
                );
            } catch (ErrorException $e) {
                error_log($e);
            }
        }

        DB::connection()->commit();
        return $respuesta;
    }



    /**
     *
     */
    public function show($id)
    {
        return CierreEtapaResource::make($this->repository->find($id));
    }

    public function getFuncionarioAsignadoEnCierreEtapa(CierreEtapaFormRequest $request)
    {
        try {

            $datosRequest = $request->validated()["data"]["attributes"];

            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])
                    ->where('id_etapa', $datosRequest['id_etapa'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            });

            return CierreEtapaCollection::make($query);
        } catch (\Exception $e) {
            error_log($e);
            $error['estado'] = false;
            $error['error'] = 'La etapa ya fue cerrada';
            return json_encode($error);
        }
    }



    /**
     *
     */
    public function getCierreByIdProcesoDisciplinario(CierreEtapaFormRequest $request)
    {
        try {

            $datosRequest = $request->validated()["data"]["attributes"];

            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])
                    ->where('id_etapa', $datosRequest['id_etapa'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            });

            return CierreEtapaCollection::make($query);
        } catch (\Exception $e) {
            error_log($e);
            $error['estado'] = false;
            $error['error'] = 'La etapa ya fue cerrada';
            return json_encode($error);
        }
    }


    public function cierreDeActuaciones(CierreEtapaFormRequest $request)
    {

        try {

            $datosRequest = $request->validated()["data"]["attributes"];

            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null, 'id_estado' => Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['finalizado']]);

            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] =  $datosRequest['id_etapa'];
            $logRequest['id_fase'] = Constants::FASE['cierre_proceso'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['finalizado'];
            $logRequest['descripcion'] = "cierre del proceso";
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] =  $datosRequest['created_user'];
            $logRequest['id_tipo_expediente'] = "";
            $logRequest['id_tipo_sub_expediente'] = "";
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['cierre_etapa'];
            $logRequest['id_fase_registro'] = null;

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            ProcesoDiciplinarioModel::where('uuid', $datosRequest['id_proceso_disciplinario'])->update(['estado' => Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['finalizado']]);

            $datosRequest['eliminado'] = false;
            CierreEtapaResource::make($this->repository->create($datosRequest));

            $error['estado'] = false;
            $error['error'] = 'El Proceso Disciplinario ha sido cerrado';

            return json_encode($error);

            DB::connection()->beginTransaction();
        } catch (\Exception $e) {
            error_log($e);
            $error['estado'] = false;
            $error['error'] = 'La etapa ya fue cerrada';
            return json_encode($error);
        }
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CierreEtapaFormRequest $request, $id)
    {
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
     *
     */
    public function getProcesosDisciplinariosEnviadosPorUsuario(CierreEtapaFormRequest $request)
    {

        $datosRequest = $request->validated()["data"]["attributes"];

        // error_log($datosRequest['created_user']);

        $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
            return $model->where('created_user', $datosRequest['created_user'])->get();
        });

        return CierreEtapaCollection::make($query);
    }


    /**
     *
     */
    public function getTipoRepartoCierreEtapa($id_proceso_disciplinario)
    {

        $tipo_proceso = DB::select("select id_tipo_proceso, id_etapa from proceso_disciplinario where uuid = '" . $id_proceso_disciplinario . "'");

        $expediente = DB::select("select id_tipo_expediente, id_tipo_queja, id_termino_respuesta, id_tipo_derecho_peticion
                            from clasificacion_radicado where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "' and estado = 1");


        if (count($expediente) > 0) {

            if ($expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {
                $reciboDatos['attributes']['id_tipo_reparto'] = 0;
            } else {
                if ($expediente[0]->id_tipo_queja != null) {
                    $sub_tipo_expediente = $expediente[0]->id_tipo_queja;
                } else if ($expediente[0]->id_termino_respuesta != null) {
                    $sub_tipo_expediente = $expediente[0]->id_termino_respuesta;
                } else if ($expediente[0]->id_tipo_derecho_peticion != null) {
                    $sub_tipo_expediente = $expediente[0]->id_tipo_derecho_peticion;
                }

                $reparto = DB::select("select id_tipo_cierre_etapa
                from cierre_etapa_configuracion
                where
                id_tipo_proceso_disciplinario = '" . $tipo_proceso[0]->id_tipo_proceso . "' and
                id_tipo_expediente = " . $expediente[0]->id_tipo_expediente . " and id_subtipo_expediente = " . $sub_tipo_expediente . " and id_etapa = " . $tipo_proceso[0]->id_etapa);

                $reciboDatos['attributes']['id_tipo_reparto'] = $reparto[0]->id_tipo_cierre_etapa;
            }

            $json['data'] = $reciboDatos;

            return json_encode($json);
        }
    }


    /**
     *
     */
    public function validarClasificado($datosRequest)
    {

        $expediente = DB::select("select uuid, id_tipo_expediente, id_tipo_queja, id_termino_respuesta, id_tipo_derecho_peticion
                            from clasificacion_radicado where id_proceso_disciplinario = '" . $datosRequest['id_proceso_disciplinario'] . "' and estado = 1");

        $aux = "select uuid, id_tipo_expediente, id_tipo_queja, id_termino_respuesta, id_tipo_derecho_peticion
        from clasificacion_radicado where id_proceso_disciplinario = '" . $datosRequest['id_proceso_disciplinario'] . "' and estado = 1";

        error_log("EXPEDIENTE: " . $aux);

        // SE VALIDA EL EXPEDIENTE
        $validarClasificacionRequest['id_clasificacion_radicado'] = $expediente[0]->uuid;
        $validarClasificacionRequest['id_etapa'] = $datosRequest['id_etapa'];
        $validarClasificacionRequest['estado'] = 1;
        $validarClasificacionRequest['created_user'] = $datosRequest['created_user'];
        $validarClasificacionRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
        $validarClasificacionRequest['eliminado'] = 0;

        $validarClasificacionModel = new ValidarClasificacionModel();
        ValidarClasificacionResource::make($validarClasificacionModel->create($validarClasificacionRequest));

        //registramos log
        $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
        $logRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
        $logRequest['id_fase'] = Constants::FASE['validacion_clasificacion'];
        $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
        $logRequest['descripcion'] = 'Se valida la clasificacion del radicado';
        $logRequest['created_user'] = auth()->user()->name;
        $logRequest['id_estado'] = 3; // Remisionado
        $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia; // Remisionado
        $logRequest['documentos'] = false;
        $logRequest['id_fase_registro'] = $expediente[0]->uuid;
        $logRequest['id_funcionario_actual'] = auth()->user()->name;
        $logRequest['id_funcionario_registra'] = auth()->user()->name;
        $logRequest['id_funcionario_asignado'] = auth()->user()->name;
        $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['ninguno'];
        ValidarClasificacionController::removerFuncionarioActualLog($datosRequest['id_proceso_disciplinario']);

        $logModel = new LogProcesoDisciplinarioModel();
        LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
    }

    public function autoRegistrarActuacion($id_proceso_disciplinario)
    {
        try {

            // Se inicializa la conexion
            //DB::connection()->beginTransaction();

            // Campos de la tabla
            $datosRequest['id_actuacion'] = 1;
            $datosRequest['usuario_accion'] = "";
            $datosRequest['id_estado_actuacion'] = 1;
            $datosRequest['documento_ruta'] = null;
            $datosRequest["estado"] = true;
            $datosRequest['created_user'] = auth()->user()->name;
            $datosRequest['updated_user'] = "";
            $datosRequest['updated_at'] = "";
            $datosRequest['uuid_proceso_disciplinario'] = $id_proceso_disciplinario;
            $datosRequest['id_etapa'] = Constants::ETAPA['investigacion_preliminar'];
            $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;
            //$datosRequest["campos_finales"] = [];
            $datosRequest["id_estado_visibilidad"] = Constants::ESTADOS_VISIBILIDAD['oculto_todos'];

            $actuacion = ActuacionesModel::create($datosRequest);
            //$array = json_decode(json_encode($respuesta));

            // Se crea los datos para la tabla de trazabilidad de las actuaciones
            $datosRequestTrazabilidad["uuid_actuacion"] = $actuacion->uuid;
            $datosRequestTrazabilidad["id_estado_actuacion"] = 5;
            $datosRequestTrazabilidad["observacion"] = "Actuación comisionado";
            $datosRequestTrazabilidad["estado"] = true;
            $datosRequestTrazabilidad['created_user'] = auth()->user()->name;
            $datosRequestTrazabilidad['id_dependencia'] = auth()->user()->id_dependencia;

            // Se manda el array del modelo con su informacion para crearlo en su tabla
            TrazabilidadActuacionesModel::create($datosRequestTrazabilidad);

            // Se guarda la ejecucion con un commit para que se ejecute
            //DB::connection()->commit();

            // Se retorna la respuesta
            $exito = new stdClass;
            $exito->estado = true;
            $exito->error = "";
            return $exito;
        } catch (\Exception $e) {
            error_log($e);
            //dd($e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = "Error al momento de dar cierre de etapa.";
            return $error;
        }
    }
}
