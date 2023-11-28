<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogProcesoDisciplinarioFormRequest;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioCollection;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Http\Utilidades\Utilidades;
use App\Models\LogProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class LogProcesoDisciplinarioController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new LogProcesoDisciplinarioModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return LogProcesoDisciplinarioCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LogProcesoDisciplinarioFormRequest $request)
    {
        try {

            $datosRequest = $request->validated()["data"]["attributes"];
            return LogProcesoDisciplinarioResource::make($this->repository->create($datosRequest));
        } catch (QueryException  $e) {

            $error['estado'] = false;
            $error['error'] = $e->getMessage();
            return json_encode($error);
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
        return LogProcesoDisciplinarioResource::make($this->repository->find($id));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
     * Trae el Log del proceso disiciplinario a nivel de etapa.
     *
     * @param  $procesoDiciplinarioUUID
     * @param  LogProcesoDisciplinarioFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function getLogEtapaByIdProcesoDisciplinario2($procesoDiciplinarioUUID, LogProcesoDisciplinarioFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];

        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID, $datosRequest) {
            return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)->where('id_tipo_log', Constants::TIPO_LOG['etapa'])
                ->orderBy('created_at', 'desc')
                ->get();
        });

        return LogProcesoDisciplinarioCollection::make($query);
    }


    public function getLogEtapaByIdProcesoDisciplinario($procesoDiciplinarioUUID)
    {
        $log_proceso_disciplinario = DB::select("SELECT
            lpd.uuid,
            lpd.id_etapa,
            (SELECT nombre FROM mas_etapa WHERE mas_etapa.id = lpd.id_etapa) AS nombre_etapa,
            lpd.id_fase,
            (SELECT nombre FROM mas_fase WHERE mas_fase.id = lpd.id_fase) AS nombre_fase,
            lpd.id_estado,
            lpd.descripcion,
            lpd.id_dependencia_origen,
            (SELECT nombre FROM mas_dependencia_origen WHERE mas_dependencia_origen.id = lpd.id_dependencia_origen) AS nombre_dependencia,
            lpd.id_funcionario_registra,
            (SELECT CONCAT(CONCAT(nombre, ' '), apellido) FROM users WHERE name = lpd.id_funcionario_registra) AS nombre_funcionario_registra,
            lpd.id_funcionario_actual,
            (SELECT CONCAT(CONCAT(nombre, ' '), apellido) FROM users WHERE name = lpd.id_funcionario_actual) AS nombre_funcionario_actual,
            lpd.id_funcionario_asignado,
            (SELECT CONCAT(CONCAT(nombre, ' '), apellido) FROM users WHERE name = lpd.id_funcionario_asignado) AS nombre_funcionario_asignado,
            lpd.id_fase_registro,
            lpd.created_at,
            lpd.id_tipo_transaccion
            FROM log_proceso_disciplinario lpd WHERE lpd.id_proceso_disciplinario = '" . $procesoDiciplinarioUUID . "'
            ORDER BY lpd.created_at DESC");

        $array = array();

        for ($cont = 0; $cont < count($log_proceso_disciplinario); $cont++) {

            $reciboDatos['id'] = $log_proceso_disciplinario[$cont]->uuid;

            $reciboDatos['attributes']['id_etapa'] = $log_proceso_disciplinario[$cont]->id_etapa;
            $reciboDatos['attributes']['nombre_etapa'] = $log_proceso_disciplinario[$cont]->nombre_etapa;
            $reciboDatos['attributes']['id_fase'] = $log_proceso_disciplinario[$cont]->id_fase;
            $reciboDatos['attributes']['nombre_fase'] = $log_proceso_disciplinario[$cont]->nombre_fase;
            $reciboDatos['attributes']['descripcion'] = $log_proceso_disciplinario[$cont]->descripcion;
            $reciboDatos['attributes']['nombre_dependencia_origen'] = $log_proceso_disciplinario[$cont]->nombre_dependencia;
            $reciboDatos['attributes']['nombre_funcionario_registra'] = $log_proceso_disciplinario[$cont]->nombre_funcionario_registra;
            $reciboDatos['attributes']['nombre_funcionario_actual'] = $log_proceso_disciplinario[$cont]->nombre_funcionario_actual;
            $reciboDatos['attributes']['nombre_funcionario_asignado'] = $log_proceso_disciplinario[$cont]->nombre_funcionario_asignado;
            $reciboDatos['attributes']['fecha_registro'] = Utilidades::getFormatoFechaDDMMYY($log_proceso_disciplinario[$cont]->created_at);
            $observacion = "";

            // ANTECEDENTE
            if ($log_proceso_disciplinario[$cont]->id_fase == Constants::FASE['antecedentes']) {

                $query = DB::select("SELECT
                    a.descripcion,
                    a.created_at
                    FROM antecedente a
                    WHERE a.uuid = '" .  $log_proceso_disciplinario[$cont]->id_fase_registro . "'");

                if (count($query) > 0) {

                    if ($log_proceso_disciplinario[$cont]->id_tipo_transaccion == Constants::TIPO_DE_TRANSACCION['inicio_proceso_disciplinario']) {
                        $reciboDatos['attributes']['nombre_actividad'] = "INICIO DE PROCESO";
                        $reciboDatos['attributes']['nombre_etapa'] = "INICIO DE PROCESO";
                        $reciboDatos['attributes']['nombre_fase'] = "";
                        $reciboDatos['attributes']['observacion_larga'] = $log_proceso_disciplinario[$cont]->descripcion;
                        $reciboDatos['attributes']['observacion_corta'] = Utilidades::getDescripcionCorta($log_proceso_disciplinario[$cont]->descripcion);
                    } else {
                        if ($log_proceso_disciplinario[$cont]->id_tipo_transaccion == Constants::TIPO_DE_TRANSACCION['inactivar']) {
                            $reciboDatos['attributes']['nombre_actividad'] = "ACTIVAR ANTECEDENTE";
                            $reciboDatos['attributes']['observacion_larga'] = $log_proceso_disciplinario[$cont]->descripcion;
                            $reciboDatos['attributes']['observacion_corta'] = Utilidades::getDescripcionCorta($log_proceso_disciplinario[$cont]->descripcion);
                            error_log("2");
                        }
                        if ($log_proceso_disciplinario[$cont]->id_tipo_transaccion == Constants::TIPO_DE_TRANSACCION['activar']) {
                            $reciboDatos['attributes']['nombre_actividad'] = "INACTIVAR ANTECEDENTE";
                            $reciboDatos['attributes']['observacion_larga'] = $log_proceso_disciplinario[$cont]->descripcion;
                            $reciboDatos['attributes']['observacion_corta'] = Utilidades::getDescripcionCorta($log_proceso_disciplinario[$cont]->descripcion);
                            error_log("3");
                        } else {
                            $reciboDatos['attributes']['nombre_actividad'] = "REGISTRO DE ANTECEDENTE";
                            $reciboDatos['attributes']['observacion_larga'] = $query[0]->descripcion;
                            $reciboDatos['attributes']['observacion_corta'] = Utilidades::getDescripcionCorta($query[0]->descripcion);
                            $reciboDatos['attributes']['fecha_registro'] = Utilidades::getFormatoFechaDDMMYY($query[0]->created_at);
                            error_log("4");
                        }
                    }
                }

                error_log("TIPO DE TRANSACCION: " . $log_proceso_disciplinario[$cont]->id_tipo_transaccion . " -- " . Constants::TIPO_DE_TRANSACCION['inicio_proceso_disciplinario']);
                error_log(json_encode($reciboDatos));
            }
            // DATOS DEL INTERESADO
            elseif ($log_proceso_disciplinario[$cont]->id_fase == Constants::FASE['datos_interesado']) {

                $query = DB::select("SELECT
                    (SELECT nombre FROM mas_tipo_sujeto_procesal WHERE id = id_tipo_sujeto_procesal) AS sujeto_procesal,
                    i.primer_nombre,
                    i.segundo_nombre,
                    i.primer_apellido,
                    i.segundo_apellido,
                    i.numero_documento,
                    i.created_at
                    FROM interesado i
                    WHERE i.uuid = '" .  $log_proceso_disciplinario[$cont]->id_fase_registro . "'");

                if (count($query) > 0) {

                    $observacion = ($query[0]->sujeto_procesal != null ? $query[0]->sujeto_procesal . " - " : "");
                    $observacion = $observacion . ($query[0]->primer_nombre != null ? $query[0]->primer_nombre . " " : "");
                    $observacion = $observacion . ($query[0]->segundo_nombre != null ? $query[0]->segundo_nombre . " " : "");
                    $observacion = $observacion . ($query[0]->primer_apellido != null ? $query[0]->primer_apellido . " " : "");
                    $observacion = $observacion . ($query[0]->segundo_apellido != null ? $query[0]->segundo_apellido . " " : "");
                    $observacion = $observacion . ($query[0]->numero_documento != null ? " - " . $query[0]->numero_documento : "");

                    if ($log_proceso_disciplinario[$cont]->id_tipo_transaccion == Constants::TIPO_DE_TRANSACCION['inactivar']) {
                        $reciboDatos['attributes']['nombre_actividad'] = "ACTIVAR INTERESADO";
                        $reciboDatos['attributes']['observacion_larga'] = $observacion . ' - ' . $log_proceso_disciplinario[$cont]->descripcion;
                        $reciboDatos['attributes']['observacion_corta'] = Utilidades::getDescripcionCorta($observacion . ' - ' . $log_proceso_disciplinario[$cont]->descripcion);
                    } elseif ($log_proceso_disciplinario[$cont]->id_tipo_transaccion == Constants::TIPO_DE_TRANSACCION['activar']) {
                        $reciboDatos['attributes']['nombre_actividad'] = "INACTIVAR INTERESADO";
                        $reciboDatos['attributes']['observacion_larga'] = $observacion . ' - ' . $log_proceso_disciplinario[$cont]->descripcion;
                        $reciboDatos['attributes']['observacion_corta'] = Utilidades::getDescripcionCorta($observacion . ' - ' . $log_proceso_disciplinario[$cont]->descripcion);
                    } else {
                        $reciboDatos['attributes']['nombre_actividad'] = "REGISTRO DE UN INTERESADO";
                        $reciboDatos['attributes']['observacion_larga'] = strtoupper($observacion);
                        $reciboDatos['attributes']['observacion_corta'] = Utilidades::getDescripcionCorta(strtoupper($observacion));
                    }

                    $reciboDatos['attributes']['fecha_registro'] = Utilidades::getFormatoFechaDDMMYY($query[0]->created_at);
                }
            }


            // CLASIFICACIÓN DEL RADICADO
            elseif ($log_proceso_disciplinario[$cont]->id_fase == Constants::FASE['clasificacion_radicado']) {

                $query = DB::select("SELECT
                cr.uuid,
                cr.id_tipo_expediente,
                (SELECT nombre FROM mas_tipo_expediente WHERE id =  id_tipo_expediente) AS nombre_tipo_expediente,
                (SELECT nombre FROM mas_tipo_queja WHERE id =  id_tipo_queja) AS tipo_queja,
                (SELECT nombre FROM mas_tipo_derecho_peticion WHERE id =  id_tipo_derecho_peticion) AS tipo_derecho_peticion,
                (SELECT nombre FROM mas_termino_respuesta WHERE id =  id_termino_respuesta) AS tipo_tutela,
                cr.created_at
                FROM clasificacion_radicado cr
                WHERE cr.uuid = '" .  $log_proceso_disciplinario[$cont]->id_fase_registro . "'");

                if (count($query) > 0) {

                    if ($query[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
                        $observacion = strtoupper($query[0]->nombre_tipo_expediente . ' ' . $query[0]->tipo_derecho_peticion);
                    } else if ($query[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
                        $observacion = strtoupper($query[0]->nombre_tipo_expediente);
                    } else if ($query[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja']) {
                        $observacion = strtoupper($query[0]->nombre_tipo_expediente . ' ' . $query[0]->tipo_queja);
                    } else if ($query[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja']) {
                        $observacion = strtoupper($query[0]->nombre_tipo_expediente . ' ' . $query[0]->tipo_tutela);
                    } else if ($query[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {
                        $observacion = strtoupper($query[0]->nombre_tipo_expediente);
                    }

                    $reciboDatos['attributes']['nombre_actividad'] = "REGISTRO DE CLASIFICACIÓN DEL RADICADO";
                    $reciboDatos['attributes']['observacion_larga'] = "EL PROCESO SE CLASIFICA COMO " . $observacion;
                    $reciboDatos['attributes']['observacion_corta'] = "EL PROCESO SE CLASIFICA COMO " . $observacion;
                    $reciboDatos['attributes']['fecha_registro'] = Utilidades::getFormatoFechaDDMMYY($query[0]->created_at);
                }
            }

            // ENTIDAD DEL INVESTIGADO
            elseif ($log_proceso_disciplinario[$cont]->id_fase == Constants::FASE['entidad_investigado']) {

                $query = DB::select("SELECT
                ei.nombre_investigado,
                ei.cargo,
                ei.created_at
                FROM entidad_investigado ei
                WHERE ei.uuid = '" .  $log_proceso_disciplinario[$cont]->id_fase_registro . "'");

                if (count($query) > 0) {

                    $observacion = $query[0]->nombre_investigado != null ? $query[0]->nombre_investigado . ' - ' . $query[0]->cargo : "NO APLICA REGISTRO DEL INVESTIGADO";

                    if ($log_proceso_disciplinario[$cont]->id_tipo_transaccion == Constants::TIPO_DE_TRANSACCION['inactivar']) {
                        $reciboDatos['attributes']['nombre_actividad'] = "ACTIVAR ENTIDAD DEL INTERESADO";
                        $reciboDatos['attributes']['observacion_larga'] = $log_proceso_disciplinario[$cont]->descripcion;
                        $reciboDatos['attributes']['observacion_corta'] = Utilidades::getDescripcionCorta($log_proceso_disciplinario[$cont]->descripcion);
                    }
                    if ($log_proceso_disciplinario[$cont]->id_tipo_transaccion == Constants::TIPO_DE_TRANSACCION['activar']) {
                        $reciboDatos['attributes']['nombre_actividad'] = "INACTIVAR ENTIDAD DEL INTERESADO";
                        $reciboDatos['attributes']['observacion_larga'] = $log_proceso_disciplinario[$cont]->descripcion;
                        $reciboDatos['attributes']['observacion_corta'] = Utilidades::getDescripcionCorta($log_proceso_disciplinario[$cont]->descripcion);
                    } else {
                        $reciboDatos['attributes']['nombre_actividad'] = "REGISTRO DE ENTIDAD DEL INTERESADO";
                        $reciboDatos['attributes']['observacion_larga'] = strtoupper($observacion);;
                        $reciboDatos['attributes']['observacion_corta'] = Utilidades::getDescripcionCorta($observacion);
                        $reciboDatos['attributes']['fecha_registro'] = Utilidades::getFormatoFechaDDMMYY($query[0]->created_at);
                    }
                }
            }

            // CIERRE DE ETAPA DE CAPTURA Y REPARTO
            elseif ($log_proceso_disciplinario[$cont]->id_fase == Constants::FASE['cierre_captura_reparto']) {

                $reciboDatos['attributes']['nombre_actividad'] = "CIERRE DE ETAPA DE CAPTURA Y REPARTO";
                $reciboDatos['attributes']['observacion_larga'] = "SE DA CIERRE DE ETAPA DE CAPTURA Y REPARTO EL " . Utilidades::getFormatoFechaDDMMYY($log_proceso_disciplinario[$cont]->created_at);
                $reciboDatos['attributes']['observacion_corta'] = null;
            }

            // VALIDACION DE LA CLASIFICACION
            elseif ($log_proceso_disciplinario[$cont]->id_fase == Constants::FASE['validacion_clasificacion']) {

                $query = DB::select("SELECT
                vc.id_clasificacion_radicado
                FROM validar_clasificacion vc
                WHERE vc.uuid = '" .  $log_proceso_disciplinario[$cont]->id_fase_registro . "'");


                if (count($query) > 0) {
                    $id_clasificado = $query[0]->id_clasificacion_radicado;
                } else {
                    $id_clasificado = $log_proceso_disciplinario[$cont]->id_fase_registro;
                }

                $query2 = DB::select("SELECT
                    cr.uuid,
                    cr.id_tipo_expediente,
                    (SELECT nombre FROM mas_tipo_expediente WHERE id =  id_tipo_expediente) AS nombre_tipo_expediente,
                    (SELECT nombre FROM mas_tipo_queja WHERE id =  id_tipo_queja) AS tipo_queja,
                    (SELECT nombre FROM mas_tipo_derecho_peticion WHERE id =  id_tipo_derecho_peticion) AS tipo_derecho_peticion,
                    (SELECT nombre FROM mas_termino_respuesta WHERE id =  id_termino_respuesta) AS tipo_tutela,
                    cr.created_at,
                    cr.reclasificacion
                    FROM clasificacion_radicado cr
                    WHERE cr.uuid = '" .  $id_clasificado . "'");


                if (count($query2) > 0) {

                    if ($query2[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
                        $observacion = $query2[0]->nombre_tipo_expediente . ' ' . $query2[0]->tipo_derecho_peticion;
                    } else if ($query2[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
                        $observacion =  $query2[0]->nombre_tipo_expediente;
                    } else if ($query2[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja']) {
                        $observacion = $query2[0]->nombre_tipo_expediente . ' ' . $query2[0]->tipo_queja;
                    } else if ($query2[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja']) {
                        $observacion = $query2[0]->nombre_tipo_expediente . ' ' . $query2[0]->tipo_tutela;
                    } else if ($query2[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {
                        $observacion = $query2[0]->nombre_tipo_expediente;
                    }

                    if (count($query) > 0) {
                        $reciboDatos['attributes']['nombre_actividad'] = "REGISTRO DE VALIDACIÓN DEL RADICADO";
                        $reciboDatos['attributes']['observacion_larga'] = "SE VALIDA CLASIFICACIÓN COMO " . $observacion;
                    } else {
                        $reciboDatos['attributes']['nombre_actividad'] = "RECLASIFICACION DE TIPO DE EXPEDIENTE";
                        $reciboDatos['attributes']['observacion_larga'] = "SE RECLASFICA EL PROCESO COMO " . $observacion;
                    }


                    $reciboDatos['attributes']['observacion_corta'] = null;
                    $reciboDatos['attributes']['fecha_registro'] = Utilidades::getFormatoFechaDDMMYY($query2[0]->created_at);
                }
            }

            // EVALUACION
            elseif ($log_proceso_disciplinario[$cont]->id_fase == Constants::FASE['evaluacion']) {

                $query = DB::select("SELECT
                    e.resultado_evaluacion,
                    (SELECT nombre FROM mas_resultado_evaluacion WHERE id = e.resultado_evaluacion) as nombre_evaluacion,
                    e.justificacion,
                    e.estado,
                    e.created_at
                    FROM evaluacion e
                    WHERE e.uuid = '" .  $log_proceso_disciplinario[$cont]->id_fase_registro . "'");

                if (count($query) > 0) {

                    if ($query[0]->estado == Constants::ESTADO_EVALUACION['registrado']) {
                        $reciboDatos['attributes']['nombre_actividad'] = "REGISTRO DE EVALUACIÓN";
                    } elseif ($query[0]->estado == Constants::ESTADO_EVALUACION['rechazado_por_jefe']) {
                        $reciboDatos['attributes']['nombre_actividad'] = "CLASIFICACIÓN DE LA EVALUACIÓN RECHAZADA";
                    } elseif ($query[0]->estado == Constants::ESTADO_EVALUACION['aprobado_por_jefe']) {
                        $reciboDatos['attributes']['nombre_actividad'] = "CLASIFICACIÓN DE LA EVALUACIÓN APROBADA";
                    }

                    $reciboDatos['attributes']['tipo_evaluacion'] = $query[0]->nombre_evaluacion;

                    $reciboDatos['attributes']['observacion_larga'] = $query[0]->justificacion;
                    $reciboDatos['attributes']['observacion_corta'] = Utilidades::getDescripcionCorta($query[0]->justificacion);
                    $reciboDatos['attributes']['fecha_registro'] = Utilidades::getFormatoFechaDDMMYY($query[0]->created_at);
                }
            }

            // REMISION QUEJA
            elseif ($log_proceso_disciplinario[$cont]->id_fase == Constants::FASE['remision_queja']) {

                $reciboDatos['attributes']['nombre_actividad'] = "REMISIÓN QUEJA";

                $query = DB::select("SELECT
                    rq.id_dependencia_destino,
                    (SELECT nombre FROM mas_dependencia_origen WHERE mas_dependencia_origen.id = rq.id_dependencia_destino) AS nombre_dependencia,
                    rq.created_at
                    FROM remision_queja rq
                    WHERE rq.uuid= '" .  $log_proceso_disciplinario[$cont]->id_fase_registro . "'");

                if (count($query) > 0) {

                    $reciboDatos['attributes']['observacion_larga'] = "CUANDO SE CIERRE LA ETAPA ACTUAL, EL PROCESO SERÁ ASIGNADO A LA DEPENDENCIA " . $query[0]->nombre_dependencia;
                    $reciboDatos['attributes']['observacion_corta'] = "CUANDO SE CIERRE LA ETAPA ACTUAL, EL PROCESO SERÁ ASIGNADO A LA DEPENDENCIA " . $query[0]->nombre_dependencia;
                    $reciboDatos['attributes']['fecha_registro'] = Utilidades::getFormatoFechaDDMMYY($query[0]->created_at);
                }
            }

            // GESTOR RESPUESTA
            elseif ($log_proceso_disciplinario[$cont]->id_fase == Constants::FASE['gestor_respuesta']) {

                $query = DB::select("SELECT
                    gr.aprobado,
                    gr.proceso_finalizado,
                    gr.descripcion,
                    gr.created_at
                    FROM gestor_respuesta gr
                    WHERE gr.uuid= '" .  $log_proceso_disciplinario[$cont]->id_fase_registro . "'");

                if (count($query) > 0) {

                    if ($query[0]->aprobado == 0) {
                        $reciboDatos['attributes']['nombre_actividad'] = "GESTOR RESPUESTA PENDIENTE DE APROBACIÓN POR EL JEFE DE LA DEPENDENCIA";
                    }
                    if ($query[0]->aprobado == 1) {
                        $reciboDatos['attributes']['nombre_actividad'] = "GESTOR RESPUESTA APROBADO POR EL JEFE DE LA DEPENDENCIA";
                    }

                    $reciboDatos['attributes']['observacion_larga'] = $query[0]->descripcion;
                    $reciboDatos['attributes']['observacion_corta'] =  $query[0]->descripcion;
                    $reciboDatos['attributes']['fecha_registro'] = Utilidades::getFormatoFechaDDMMYY($query[0]->created_at);
                }
            }

            // SE VALIDA SI HAY TRANSFERENCIA DE PROCESO DISCIPLINARIO
            if ($log_proceso_disciplinario[$cont]->id_funcionario_registra != $log_proceso_disciplinario[$cont]->id_funcionario_asignado) {
                $reciboDatos['attributes']['transferencia'] = "El proceso se transfiere al usuario " . $reciboDatos['attributes']['nombre_funcionario_asignado'];
            } else {
                $reciboDatos['attributes']['transferencia'] = null;
            }

            array_push($array, $reciboDatos);
            $reciboDatos = null;
        }

        $json['data'] = $array;
        return json_encode($json);
    }




    public function getLogByIdFaseRegistro($idFaseRegistro)
    {

        $query = $this->repository->customQuery(function ($model) use ($idFaseRegistro) {
            return $model->where('id_fase_registro', $idFaseRegistro)
                ->orderBy('created_at', 'desc')
                ->get();
        });

        return LogProcesoDisciplinarioCollection::make($query);
    }


    /**
     * Trae el Log del proceso disiciplinario a nivel de etapa.
     *
     * @param  $procesoDiciplinarioUUID
     * @param  LogProcesoDisciplinarioFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function getLogReExpByIdProcesoDisciplinario($procesoDiciplinarioUUID)
    {
        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID) {
            return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)
                ->leftJoin('interesado', 'comunicacion_interesado.id_interesado', '=', 'interesado.uuid')
                ->where('id_tipo_transaccion', Constants::TIPO_DE_TRANSACCION['reclasificacion_expediente'])
                ->orderBy('created_at', 'desc')
                ->get();
        });

        return LogProcesoDisciplinarioCollection::make($query);
    }

    /**
     *
     *
     */
    public function getLogCierreEtapa(LogProcesoDisciplinarioFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];

        // error_log($datosRequest['id_proceso_disciplinario']);

        $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
            return $model->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])
                ->where('id_tipo_transaccion', Constants::TIPO_DE_TRANSACCION['cierre_etapa'])
                ->where('id_etapa', $datosRequest['id_etapa'])
                ->get();
        });

        return LogProcesoDisciplinarioCollection::make($query);
    }

    /**
     *
     */
    public function getReporteCasosAsignadosPorUsuario()
    {

        $results = DB::select("
            SELECT
            te.user_id as id_funcionario,
            u.nombre as nombre_funcionario,
            u.apellido as apellido_funcionario,
            u.name as usuario,
            d.nombre as dependencia,
            u.reparto_habilitado as habilitado,
            u.estado as estado,
            (select count(*) FROM log_proceso_disciplinario where id_funcionario_actual = u.name) as num_casos_total,
            (SELECT LISTAGG (tdp_.nombre, ',') WITHIN GROUP (ORDER BY tdp_.nombre) roles
            FROM users_tipo_expediente te_
            inner join mas_tipo_derecho_peticion tdp_ on tdp_.id = te_.sub_tipo_expediente_id
            where te_.user_id = te.user_id
            and te_.tipo_expediente_id = 1
            GROUP BY te_.user_id) derechos_peticion,
            (SELECT LISTAGG (tdp_.nombre, ',') WITHIN GROUP (ORDER BY tdp_.nombre) roles
            FROM users_tipo_expediente te_
            inner join mas_tipo_queja tdp_ on tdp_.id = te_.sub_tipo_expediente_id
            where te_.user_id = te.user_id
            and te_.tipo_expediente_id = 2
            GROUP BY te_.user_id) poder_preferente,
            (SELECT LISTAGG (tdp_.nombre, ',') WITHIN GROUP (ORDER BY tdp_.nombre) roles
            FROM users_tipo_expediente te_
            inner join mas_tipo_queja tdp_ on tdp_.id = te_.sub_tipo_expediente_id
            where te_.user_id = te.user_id
            and te_.tipo_expediente_id = 3
            GROUP BY te_.user_id) queja,
            (SELECT LISTAGG (tdp_.nombre, ',') WITHIN GROUP (ORDER BY tdp_.nombre) roles
            FROM users_tipo_expediente te_
            inner join mas_termino_respuesta tdp_ on tdp_.id = te_.sub_tipo_expediente_id
            where te_.user_id = te.user_id
            and te_.tipo_expediente_id = 4
            GROUP BY te_.user_id) tutela
            FROM users_tipo_expediente te
            inner join users u on u.id = te.user_id
            inner join mas_tipo_expediente me on me.id = te.tipo_expediente_id
            inner join mas_tipo_derecho_peticion tdp on tdp.id = te.sub_tipo_expediente_id
            inner join mas_dependencia_origen d on d.id = u.id_dependencia
            GROUP BY te.user_id, u.nombre, u.apellido, u.name, d.nombre, u.reparto_habilitado, u.estado
            ORDER BY u.nombre");


        $array = array(); //creamos un array

        for ($cont = 0; $cont < count($results); $cont++) {
            $reciboDatos['attributes']['id_funcionario'] = $results[$cont]->id_funcionario;
            $reciboDatos['attributes']['nombre_funcionario'] = mb_strtoupper($results[$cont]->nombre_funcionario . ' ' . $results[$cont]->apellido_funcionario);
            $reciboDatos['attributes']['usuario'] = $results[$cont]->usuario;
            $reciboDatos['attributes']['dependencia'] = mb_strtoupper($results[$cont]->dependencia);
            $reciboDatos['attributes']['habilitado'] = $results[$cont]->habilitado ? "SI" : "NO";
            $reciboDatos['attributes']['estado'] = $results[$cont]->estado ? "ACTIVO" : "INACTIVO";
            $reciboDatos['attributes']['num_casos_total'] = $results[$cont]->num_casos_total;
            $reciboDatos['attributes']['derechos_peticion'] = mb_strtoupper($results[$cont]->derechos_peticion);
            $reciboDatos['attributes']['poder_preferente'] = mb_strtoupper($results[$cont]->poder_preferente);
            $reciboDatos['attributes']['queja'] = mb_strtoupper($results[$cont]->queja);
            $reciboDatos['attributes']['tutela'] = mb_strtoupper($results[$cont]->tutela);

            array_push($array, $reciboDatos);
        }

        $json['data'] = $array;
        return json_encode($json);
    }


    /**
     *
     */
    public function getReporteCasos()
    {
        $procesosTotal = DB::select("select count(*) as total FROM proceso_disciplinario");
        $procesosActivos = DB::select("select count(*) as total FROM proceso_disciplinario where estado = 1");
        $procesosCerrados = DB::select("select count(*) as total FROM proceso_disciplinario where estado = 2");
        $procesosArchivados = DB::select("select count(*) as total FROM proceso_disciplinario where estado = 3");

        $reciboDatos['total'] =  $procesosTotal[0]->total;
        $reciboDatos['activos'] =  $procesosActivos[0]->total;
        $reciboDatos['cerrados'] =  $procesosCerrados[0]->total;
        $reciboDatos['archivados'] =  $procesosArchivados[0]->total;

        $json['data'] = $reciboDatos;
        return json_encode($json);
    }


    /**
     *
     */
    public function getReporteDetallado($user)
    {

        $cantTotal = DB::select("select count(*) total
        from log_proceso_disciplinario
        where id_funcionario_actual = '" . $user . "'");
        $reciboDatos['total'] =  $cantTotal[0]->total;

        /**
         * DERECHO DE PETICION
         **/
        $cantDerechoPeticion = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 1 and cr.estado = 1");
        $reciboDatosDP['total'] =  $cantDerechoPeticion[0]->total;

        // COPIAS
        $cantDerechoPeticionCopias = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 1 and cr.id_tipo_derecho_peticion = 1 and cr.estado = 1");

        $reciboDatosDP['copias'] = $cantDerechoPeticionCopias[0]->total;

        // GENERAL
        $cantDerechoPeticionGeneral = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 1 and cr.id_tipo_derecho_peticion = 2 and cr.estado = 1");
        $reciboDatosDP['general'] = $cantDerechoPeticionGeneral[0]->total;

        // ALERTA CONTROL POLITICO
        $cantDerechoControlPolitico = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 1 and cr.id_tipo_derecho_peticion = 3 and cr.estado = 1");
        $reciboDatosDP['control_politico'] = $cantDerechoControlPolitico[0]->total;


        /**
         *PODER PREFERENTE
         **/
        $cantPoderPreferente = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 2  and cr.estado = 1");
        $reciboDatosPP['total'] =  $cantPoderPreferente[0]->total;

        /**
         *QUEJA
         **/
        //TOTAL
        $cantQueja = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 3  and cr.estado = 1");
        $reciboDatosQ['total'] =  $cantQueja[0]->total;


        // INTERNA
        $cantQuejaInterna = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 3 and cr.id_tipo_queja = 2 and cr.estado = 1");
        $reciboDatosQ['interna'] = $cantQuejaInterna[0]->total;

        // EXTERNA
        $cantQuejaExterna = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 3 and cr.id_tipo_queja = 1 and cr.estado = 1");
        $reciboDatosQ['externa'] = $cantQuejaExterna[0]->total;

        /**
         *TUTELA
         **/
        // TOTAL
        $cantTutela = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 4  and cr.estado = 1");
        $reciboDatosT['total'] =  $cantTutela[0]->total;

        // DIAS
        $cantTutelaDias = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 4 and cr.id_termino_respuesta = 1  and cr.estado = 1");
        $reciboDatosT['dias'] = $cantTutelaDias[0]->total;

        // TUTELA
        // HORAS
        $cantTutelaHoras = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 4 and cr.id_termino_respuesta = 2  and cr.estado = 1");
        $reciboDatosT['horas'] = $cantTutelaHoras[0]->total;

        /**
         *PROCESO DISCIPLINARIO
         **/
        $cantProcesoDisciplinario = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . $user . "' and cr.id_tipo_expediente = 5 and cr.estado = 1");
        $reciboDatosPD['total'] =  $cantProcesoDisciplinario[0]->total;

        $json['data']['attributes'] = $reciboDatos;
        $json['data']['attributes']['derechoPeticion'] = $reciboDatosDP;
        $json['data']['attributes']['queja'] = $reciboDatosQ;
        $json['data']['attributes']['poderPreferente'] = $reciboDatosPP;
        $json['data']['attributes']['tutela'] = $reciboDatosT;
        $json['data']['attributes']['procesoDisciplinario'] = $reciboDatosPD;

        return json_encode($json);
    }

    /**
     *
     */
    public function getReporteCasosAsignadosPorDependencia()
    {

        $results = DB::select("select
            count(mdo.nombre) as casos_por_dependencia,
            mdo.nombre as nombre_dependencia,
            mdo.id as id_dependencia
            from log_proceso_disciplinario lpd
            inner join users u on u.name = lpd.id_funcionario_asignado
            inner join mas_dependencia_origen mdo on mdo.id = u.id_dependencia
            where lpd.id_funcionario_actual is not null
            group by mdo.nombre, mdo.id
            order by mdo.nombre");

        $array = array(); //creamos un array

        for ($cont = 0; $cont < count($results); $cont++) {
            $reciboDatos['attributes']['casos_por_dependencia'] = $results[$cont]->casos_por_dependencia;
            $reciboDatos['attributes']['nombre_dependencia'] = $results[$cont]->nombre_dependencia;
            $reciboDatos['attributes']['id_dependencia'] = $results[$cont]->id_dependencia;

            array_push($array, $reciboDatos);
        }

        $json['data'] = $array;
        return json_encode($json);
    }


    /**
     *
     */
    public function getReporteDetalladoPorDependencia($id_dependencia)
    {

        $cantTotal = DB::select("select count(*) total
        from log_proceso_disciplinario
        where id_dependencia_origen = '" . $id_dependencia . "'  and id_funcionario_actual is not null");
        $reciboDatos['total'] =  $cantTotal[0]->total;

        /**
         * DERECHO DE PETICION
         **/
        $cantDerechoPeticion = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 1 and cr.estado = 1 and lpd.id_funcionario_actual is not null");
        $reciboDatosDP['total'] =  $cantDerechoPeticion[0]->total;

        // COPIAS
        $cantDerechoPeticionCopias = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 1 and cr.id_tipo_derecho_peticion = 1 and cr.estado = 1 and lpd.id_funcionario_actual is not null");

        $reciboDatosDP['copias'] = $cantDerechoPeticionCopias[0]->total;

        // GENERAL
        $cantDerechoPeticionGeneral = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 1 and cr.id_tipo_derecho_peticion = 2 and cr.estado = 1 and lpd.id_funcionario_actual is not null");
        $reciboDatosDP['general'] = $cantDerechoPeticionGeneral[0]->total;

        // ALERTA CONTROL POLITICO
        $cantDerechoControlPolitico = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 1 and cr.id_tipo_derecho_peticion = 3 and cr.estado = 1 and lpd.id_funcionario_actual is not null");
        $reciboDatosDP['control_politico'] = $cantDerechoControlPolitico[0]->total;


        /**
         *PODER PREFERENTE
         **/
        $cantPoderPreferente = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 2  and cr.estado = 1 and lpd.id_funcionario_actual is not null");
        $reciboDatosPP['total'] =  $cantPoderPreferente[0]->total;

        /**
         *QUEJA
         **/
        //TOTAL
        $cantQueja = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 3  and cr.estado = 1 and lpd.id_funcionario_actual is not null");
        $reciboDatosQ['total'] =  $cantQueja[0]->total;


        // INTERNA
        $cantQuejaInterna = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 3 and cr.id_tipo_queja = 2 and cr.estado = 1 and lpd.id_funcionario_actual is not null");
        $reciboDatosQ['interna'] = $cantQuejaInterna[0]->total;

        // EXTERNA
        $cantQuejaExterna = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 3 and cr.id_tipo_queja = 1 and cr.estado = 1 and lpd.id_funcionario_actual is not null");
        $reciboDatosQ['externa'] = $cantQuejaExterna[0]->total;

        /**
         *TUTELA
         **/
        // TOTAL
        $cantTutela = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 4  and cr.estado = 1 and lpd.id_funcionario_actual is not null");
        $reciboDatosT['total'] =  $cantTutela[0]->total;

        // DIAS
        $cantTutelaDias = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 4 and cr.id_termino_respuesta = 1  and cr.estado = 1 and lpd.id_funcionario_actual is not null");
        $reciboDatosT['dias'] = $cantTutelaDias[0]->total;

        // TUTELA
        // HORAS
        $cantTutelaHoras = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 4 and cr.id_termino_respuesta = 2  and cr.estado = 1 and lpd.id_funcionario_actual is not null");
        $reciboDatosT['horas'] = $cantTutelaHoras[0]->total;

        /**
         *PROCESO DISCIPLINARIO
         **/
        $cantProcesoDisciplinario = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_dependencia_origen = '" . $id_dependencia . "' and cr.id_tipo_expediente = 5 and cr.estado = 1 and lpd.id_funcionario_actual is not null");
        $reciboDatosPD['total'] =  $cantProcesoDisciplinario[0]->total;

        $json['data']['attributes'] = $reciboDatos;
        $json['data']['attributes']['derechoPeticion'] = $reciboDatosDP;
        $json['data']['attributes']['queja'] = $reciboDatosQ;
        $json['data']['attributes']['poderPreferente'] = $reciboDatosPP;
        $json['data']['attributes']['tutela'] = $reciboDatosT;
        $json['data']['attributes']['procesoDisciplinario'] = $reciboDatosPD;

        return json_encode($json);
    }
}
