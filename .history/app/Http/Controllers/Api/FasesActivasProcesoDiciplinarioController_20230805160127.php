<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Controllers\Traits\SiriusTrait;
use App\Http\Controllers\Traits\UtilidadesTrait;
use App\Http\Utilidades\Constants;
use App\Models\ProcesoDiciplinarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Support\Facades\DB;

class FasesActivasProcesoDiciplinarioController extends Controller
{
    use SiriusTrait;
    use LogTrait;
    use UtilidadesTrait;

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ProcesoDiciplinarioModel());
    }

    /**
     *
     */
    public function getFasesProcesoDisciplinario($procesoDisciplinarioUUID)
    {

        $log = DB::select("select mas_fase.id as id_fase, mas_fase.nombre, mas_fase.id_etapa,
        (select count(log_proceso_disciplinario.id_fase) from log_proceso_disciplinario
        where log_proceso_disciplinario.id_fase = mas_fase.id and log_proceso_disciplinario.id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "') as num_registros
        from mas_fase");

        /*$aux = "select mas_fase.id as id_fase, mas_fase.nombre, mas_fase.id_etapa,
        (select count(log_proceso_disciplinario.id_fase) from log_proceso_disciplinario
        where log_proceso_disciplinario.id_fase = mas_fase.id and log_proceso_disciplinario.id_proceso_disciplinario = '".$procesoDisciplinarioUUID."') as num_registros
        from mas_fase";

        error_log($aux);*/

        $lista = array();
        $cierre_captura_reparto = false;
        $cierre_evaluacion = false;

        foreach ($log as $item) {

            $request['type'] = "fases_del_proceso";
            $request['id'] = $item->id_fase;
            $request['attributes']['nombre'] = mb_strtoupper(FasesActivasProcesoDiciplinarioController::getNombreFase($item->id_fase));
            $request['attributes']['etapa'] = $item->id_etapa;
            $request['attributes']['visible'] = false;
            $request['attributes']['semaforizacion'] =  Constants::SEMAFORIZACION['red'];

            if ($item->id_etapa == Constants::ETAPA['captura_reparto']) {
                $request['attributes']['visible'] = true;
            }

            if ($item->num_registros > 0) {
                $request['attributes']['estado'] = true;
                $request['attributes']['semaforizacion'] =  Constants::SEMAFORIZACION['green'];
            } else {
                $request['attributes']['estado'] = false;
            }

            if ($item->id_fase == Constants::FASE['cierre_captura_reparto'] && $item->num_registros > 0) {
                $cierre_captura_reparto = true;
            }

            if ($item->id_fase == Constants::FASE['cierre_evaluacion'] && $item->num_registros > 0) {
                $cierre_evaluacion = true;
            }

            array_push($lista, $request);
        }

        // 1. VALIDAR CAPTURA Y REPARTO
        $array['data'] = $this->validarCapturaReparto($log, $cierre_captura_reparto, $lista);

        // VALIDAR EL TIPO DE EXPEDIENTE.
        $tipo_expediente = DB::select("select id_tipo_expediente, id_tipo_queja, id_tipo_derecho_peticion, id_termino_respuesta from clasificacion_radicado
        where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and estado = 1");


        // TIPO DE EXPEDIENTE QUEJA
        if ($tipo_expediente != null && ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja'] || $tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario'])) {

            error_log("ESTOY DENTRO DE EVALUACION QUEJA 2");

            // Se valida si ya habia sido rechazada la evaluacion
            $evaluacion = DB::select("select resultado_evaluacion from evaluacion where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and estado_evaluacion = 1 and estado = 3");

            if (count($evaluacion) == 0) {
                $evaluacion = DB::select("select resultado_evaluacion from evaluacion where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and estado_evaluacion = 1 and estado <> 1");
            }

            if (count($evaluacion) > 0) {
                error_log("P1 **");
                $array = $this->activarFasePorTipoEvaluacion($procesoDisciplinarioUUID, $array, $evaluacion[0]->resultado_evaluacion, $tipo_expediente[0]->id_tipo_expediente, $tipo_expediente[0]->id_tipo_queja, $log, $cierre_evaluacion);
            } else {
                error_log("P2 **");
                $array = $this->activarFasePorTipoEvaluacion($procesoDisciplinarioUUID, $array, 1, $tipo_expediente[0]->id_tipo_expediente, $tipo_expediente[0]->id_tipo_queja, $log, $cierre_evaluacion);
            }
        }

        // TIPO DE EXPEDIENTE TUTELA
        else if ($tipo_expediente != null && $tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']) {
            $array = $this->activarFasePorTipoEvaluacion($procesoDisciplinarioUUID, $array, Constants::RESULTADO_EVALUACION['sin_evaluacion'], $tipo_expediente[0]->id_tipo_expediente, $tipo_expediente[0]->id_termino_respuesta, $log, $cierre_evaluacion);
        }


        // TIPO DE EXPEDIENTE DERECHO DE PETICION
        else if ($tipo_expediente != null && $tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
            $array = $this->activarFasePorTipoEvaluacion($procesoDisciplinarioUUID, $array, Constants::RESULTADO_EVALUACION['sin_evaluacion'], $tipo_expediente[0]->id_tipo_expediente, $tipo_expediente[0]->id_tipo_derecho_peticion, $log, $cierre_evaluacion);
        }

        // TIPO DE EXPEDIENTE DERECHO DE PETICION
        else if ($tipo_expediente != null && $tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
            $array = $this->activarFasePorTipoEvaluacion($procesoDisciplinarioUUID, $array, Constants::RESULTADO_EVALUACION['sin_evaluacion'], $tipo_expediente[0]->id_tipo_expediente, $tipo_expediente[0]->id_tipo_queja, $log, $cierre_evaluacion);
        }

        return json_encode($array);
    }

    /**
     *  VALIDAR CIERRE ETAPA DE EVALUACION INCORPORACION, COMISORIO EJE
     */
    public function validarCierreEtapaCapturaReparto($log, $cierre_captura_reparto)
    {

        // VALIDAR SI LA ETAPA DE CAPTURA ESTA CERRADA O ESTA LISTA PARA INICIAR CIERRE
        $band = true;
        $cont = 0;

        while ($band && $cont < count($log) && !$cierre_captura_reparto) {

            if ($log[$cont]->id_fase == Constants::FASE['antecedentes']) {
                if ($log[$cont]->num_registros > 0) {
                    $band = true;
                    $cierre_captura_reparto = false;
                } else {
                    $band = false;
                    $cont = count($log);
                    $cierre_captura_reparto = true;
                }

                error_log("antecedentes:");
            } else if ($log[$cont]->id_fase == Constants::FASE['datos_interesado']) {
                if ($log[$cont]->num_registros > 0) {
                    $band = true;
                    $cierre_captura_reparto = false;
                    error_log("datos_interesado");
                } else {
                    $band = false;
                    $cont = count($log);
                    $cierre_captura_reparto = true;
                }

                error_log("datos_intersado");
            } else if ($log[$cont]->id_fase == Constants::FASE['clasificacion_radicado']) {
                if ($log[$cont]->num_registros > 0) {
                    $band = true;
                    $cierre_captura_reparto = false;
                    error_log("clasificacion_radicado");
                } else {
                    $band = false;
                    $cont = count($log);
                    $cierre_captura_reparto = true;
                }

                error_log("clasificacion_radicado");
            } else if ($log[$cont]->id_fase == Constants::FASE['entidad_investigado']) {
                if ($log[$cont]->num_registros > 0) {
                    $band = true;
                    $cierre_captura_reparto = false;
                    // error_log("entidad_investigado");
                } else {
                    $band = false;
                    $cont = count($log);
                    $cierre_captura_reparto = true;
                }

                error_log("entidad_investigado");
            } else if ($log[$cont]->id_fase == Constants::FASE['soporte_radicado']) {
                if ($log[$cont]->num_registros > 0) {
                    $band = true;
                    $cierre_captura_reparto = false;
                    error_log("soporte_radicado");
                } else {
                    $band = false;
                    $cont = count($log);
                    $cierre_captura_reparto = true;
                }

                error_log("soporte_radicado");
            } else if ($log[$cont]->id_fase == Constants::FASE['cierre_captura_reparto']) {
                if ($log[$cont]->num_registros > 0) {
                    $band = false;
                    $cont = count($log);
                    $cierre_captura_reparto = true;
                } else {
                    $band = true;
                    $cierre_captura_reparto = false;
                    error_log("cierre_captura_reparto");
                }

                error_log("cierre_captura_reparto");
            }

            $cont++;
        }

        return $cierre_captura_reparto;
    }

    /**
     * VALIDAR CIERRE ETAPA CAPTURA Y REPARTO
     */
    public function validarCapturaReparto($log, $cierre_captura_reparto, $lista)
    {

        $cierre_captura_reparto =  $this->validarCierreEtapaCapturaReparto($log, $cierre_captura_reparto);
        // error_log("CIERRE CAPTURA Y REPARTO: ".$cierre_captura_reparto);

        if (!$cierre_captura_reparto) {
            $request['type'] = "fases_del_proceso";
            $request['id'] = Constants::FASE['lista_para_cierre_captura_reparto'];
            $request['attributes']['nombre'] = "lista_para_cierre_captura_reparto";
            $request['attributes']['estado'] = true;
            $request['attributes']['visible'] = true;
            $request['attributes']['semaforizacion'] =  Constants::SEMAFORIZACION['red'];
            array_push($lista, $request);
        }

        return $lista;
    }

    /**
     *
     */
    public function activarFasePorTipoEvaluacion($procesoDisciplinarioUUID, $array, $id_resultado_evaluacion, $id_tipo_expediente, $id_subtipo_expediente, $log, $cierre_evaluacion)
    {

        $lista_fases_activas = DB::select("select id_fase_actual, id_fase_antecesora from evaluacion_fase
            where id_resultado_evaluacion = " . $id_resultado_evaluacion . " and id_tipo_expediente = " . $id_tipo_expediente . " and id_sub_tipo_expediente = " . $id_subtipo_expediente . "  order by orden");


        for ($cont = 0; $cont < count($lista_fases_activas); $cont++) {

            $fase_antecesora_terminada = 0;
            $fase_actual = $this->getEstadoFaseProcesoDisciplinario($procesoDisciplinarioUUID, $lista_fases_activas[$cont]->id_fase_actual);
            $fase_actual_terminada = $this->validarEstadoFase($lista_fases_activas[$cont]->id_fase_actual, $procesoDisciplinarioUUID);
            $fase_antecesora_terminada = $this->validarEstadoFase($lista_fases_activas[$cont]->id_fase_antecesora, $procesoDisciplinarioUUID);

            /*error_log("FASE ACTUAL " . $fase_actual);
            error_log("FASE ACTUAL TERMINADA " . $fase_actual_terminada);
            error_log("FASE ANTECESORA TERMINADA " . $fase_antecesora_terminada);
            error_log("");
            error_log("");*/

            for ($cont2 = 0; $cont2 < count($array['data']); $cont2++) {

                if ($array['data'][$cont2]['id'] == $lista_fases_activas[$cont]->id_fase_actual) {

                    if ($fase_actual == 0 && $fase_antecesora_terminada == 3 && $fase_actual_terminada == 1) {
                        $array['data'][$cont2]['attributes']['visible'] = true;
                        $array['data'][$cont2]['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['red'];
                    } else if ($fase_actual == 1 && $fase_antecesora_terminada == 3 && $fase_actual_terminada == 2) {
                        $array['data'][$cont2]['attributes']['visible'] = true;
                        $array['data'][$cont2]['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['orange'];
                    } else if ($fase_actual == 1 && $fase_antecesora_terminada == 3 && $fase_actual_terminada == 1) {
                        $array['data'][$cont2]['attributes']['visible'] = true;
                        $array['data'][$cont2]['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['orange'];
                    } else if ($fase_actual == 1 && $fase_antecesora_terminada == 3 && $fase_actual_terminada == 3) {
                        $array['data'][$cont2]['attributes']['visible'] = true;
                        $array['data'][$cont2]['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['green'];
                    }

                    $cont2 = count($array['data']);
                }
            }


            if ($lista_fases_activas[$cont]->id_fase_actual == Constants::FASE['cierre_evaluacion']) {

                $cierre_evaluacion = $this->validarCierreEtapa($log, $cierre_evaluacion, $lista_fases_activas[$cont]->id_fase_antecesora);

                if (!$cierre_evaluacion) {
                    $request['type'] = "fases_del_proceso";
                    $request['id'] = Constants::FASE['lista_para_cierre_evaluacion'];
                    $request['attributes']['nombre'] = "lista_para_cierre_evaluacion";
                    $request['attributes']['estado'] = true;
                    $request['attributes']['visible'] = true;
                    array_push($array['data'], $request);
                }
            }
        }

        /**************************************************************************************
         * SI LA ETAPA YA ESTA CERRADA NO DEBERIA MOSTRAR FASES PENDIENTES POR DILIGENCIAR.
         * ESTO PASA CUANDO UN EXPEDIENTE PASA A PROCESO DISCIPLINARIO Y
         * CUANDO SE MIGRA UN PROCESO
         **************************************************************************************/
        $etapa_cerrada = DB::select("select id_etapa from proceso_disciplinario where uuid = '" . $procesoDisciplinarioUUID . "'");

        if (!empty($etapa_cerrada) && $etapa_cerrada[0]->id_etapa > 2 && $id_tipo_expediente = Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {

            for ($cont1 = 0; $cont1 < count($array['data']); $cont1++) {
                if ($array['data'][$cont1]['attributes']['etapa'] == 2 &&  $array['data'][$cont1]['attributes']['semaforizacion'] == Constants::SEMAFORIZACION['red']) {
                    error_log($array['data'][$cont1]['attributes']['nombre']);
                    $array['data'][$cont1]['attributes']['visible'] = false;
                }
            }
        }

        return $array;
    }


    /**
     *
     */
    public function validarEstadoFase($id_fase, $procesoDisciplinarioUUID)
    {

        $fase_terminada = 0;
        $fase_en_proceso = 0;

        if ($id_fase == Constants::FASE['cierre_captura_reparto']) {
            error_log("CIERRE CAPTURA REPARTO");
            $fase_terminada = count(DB::select("select uuid from log_proceso_disciplinario where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and id_fase = 14 and eliminado = 0"));
        } else if ($id_fase == Constants::FASE['validacion_clasificacion']) {
            error_log("VALIDAR CLASIFICADO");
            $fase_terminada = count(DB::select("select uuid from validar_clasificacion where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and eliminado = 0"));
        } else if ($id_fase == Constants::FASE['evaluacion']) {
            error_log("EVALUACION");
            $evaluacion_v = DB::select("select resultado_evaluacion from evaluacion where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and eliminado = 0");
            $evaluacion_r = DB::select("select resultado_evaluacion from evaluacion where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "'and eliminado = 0 and estado = " . Constants::ESTADO_EVALUACION['registrado']);
            $evaluacion_t = DB::select("select resultado_evaluacion from evaluacion where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "'and eliminado = 0 and (estado = " . Constants::ESTADO_EVALUACION['aprobado_por_jefe'] . " or estado = " . Constants::ESTADO_EVALUACION['rechazado_por_jefe'] . ")");

            if (count($evaluacion_v) == 0) {
                $fase_terminada = 0;
            }
            if (count($evaluacion_r) > 0) {
                $fase_en_proceso = 1;
            }
            if (count($evaluacion_t) > 0) {
                $fase_terminada = 1;
                $fase_en_proceso = 0;
            }
        } elseif ($id_fase == Constants::FASE['remision_queja']) {
            error_log("REMISION QUEJA");
            $fase_terminada =  count(DB::select("select uuid from remision_queja where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and eliminado = 0"));
        } elseif ($id_fase == Constants::FASE['gestor_respuesta']) {
            error_log("GESTOR RESPUESTA");
            $fase_terminada = count(DB::select("select uuid from gestor_respuesta where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and proceso_finalizado = 1 and eliminado = 0"));

            if ($fase_terminada == 0) {
                $fase_en_proceso = count(DB::select("select uuid from gestor_respuesta where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and proceso_finalizado = 0 and eliminado = 0"));
            }
        } elseif ($id_fase == Constants::FASE['comunicacion_interesado']) {
            error_log("COMUNICACION DEL INTERESADO");
            $fase_terminada =  count(DB::select("select uuid from comunicacion_interesado where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and estado = 1 and eliminado = 0"));
        } elseif ($id_fase == Constants::FASE['documento_cierre']) {
            error_log("DOCUMENTO CIERRE");
            $fase_terminada =  count(DB::select("select uuid from documento_cierre where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and estado = 1 and eliminado = 0"));
        } elseif ($id_fase == Constants::FASE['requerimiento_juzgado']) {
            error_log("REQUERIMIENTO JUZGADO");
            $fase_terminada =  count(DB::select("select uuid from requerimiento_juzgado where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and eliminado = 0"));
        } elseif ($id_fase == Constants::FASE['informe_cierre']) {
            error_log("INFORME CIERRE");
            $fase_terminada = count(DB::select("select uuid from informe_cierre where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and finalizado = 1 and eliminado = 0"));

            if ($fase_terminada == 0) {
                $fase_en_proceso = count(DB::select("select uuid from informe_cierre where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and finalizado = 0 and eliminado = 0"));
            }
        } elseif ($id_fase == Constants::FASE['registro_seguimiento']) {
            error_log("REGISTRO SEGUIMIENTO");
            $fase_terminada = count(DB::select("select uuid from informe_cierre where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and finalizado = 1 and eliminado = 0"));

            if ($fase_terminada == 0) {
                $fase_en_proceso = count(DB::select("select uuid from informe_cierre where id_proceso_disciplinario = '" . $procesoDisciplinarioUUID . "' and finalizado = 0 and eliminado = 0"));
            }
        }


        if ($fase_en_proceso > 0) {
            return 2;
        }

        if ($fase_terminada > 0) {
            return 3;
        }

        return 1;
    }


    /**
     *  VALIDAR CIERRE ETAPA CUANDO EL TIPO DE EXPEDIENTE ES DERECHO DE PETICION
     *
     */
    public function validarCierreEtapa($log, $cierre_evaluacion, $fase_cierre)
    {

        $band = true;
        $cont = 0;

        while ($band && $cont < count($log) && !$cierre_evaluacion) {

            if ($log[$cont]->id_fase == $fase_cierre) {
                if ($log[$cont]->num_registros > 0) {
                    $cierre_evaluacion = false;
                    $band = true;
                } else {
                    $band = false;
                    $cierre_evaluacion = true;
                    $cont = count($log);
                }
            }

            $cont++;
        }

        return $cierre_evaluacion;
    }
}
