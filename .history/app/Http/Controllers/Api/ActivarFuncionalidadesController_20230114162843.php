<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Controllers\Traits\UtilidadesTrait;
use App\Http\Utilidades\Constants;
use Illuminate\Support\Facades\DB;



/**
 * [Description ActivarFuncionalidadesController]
 */
class ActivarFuncionalidadesController extends Controller
{
    use LogTrait;
    use UtilidadesTrait;

    /**
     * Activa los tipos de conducta cuando la etapa del proceso esta en la etapa de evaluación en adelante y la fase evaluación en adelante.
     * @param mixed $id_proceso_disciplinario
     *
     * @return json.
     */
    public function activarClasificacionTiposConducta($id_proceso_disciplinario)
    {

        $id_etapa = $this->etapaActual($id_proceso_disciplinario);
        $fase_evaluacion = count(DB::select("select * from evaluacion where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "' and estado_evaluacion = 1 and estado = 2"));

        if ($id_etapa != Constants::ETAPA['captura_reparto'] && $fase_evaluacion > 0) {
            $reciboDatos['activar_funcionalidad'] = true;
        } else {
            $reciboDatos['activar_funcionalidad'] = false;
        }

        $json['data']['attributes'] = $reciboDatos;
        return json_encode($json);
    }

    /**
     * Activa la reclasificación del tipo de expediente cuando la etapa del proceso esta en la etapa de evaluación en adelante y la fase evaluación en adelante.
     * @param mixed $id_proceso_disciplinario
     *
     * @return json
     */
    public function activarClasificacionTiposExpediente($id_proceso_disciplinario)
    {

        $id_etapa = $this->etapaActual($id_proceso_disciplinario);
        $fase_clasificacion = count(DB::select("select * from validar_clasificacion where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "'"));

        if ($id_etapa != Constants::ETAPA['captura_reparto'] && $fase_clasificacion > 0) {
            $reciboDatos['activar_funcionalidad'] = true;
        } else {
            $reciboDatos['activar_funcionalidad'] = false;
        }

        $json['data']['attributes'] = $reciboDatos;
        return json_encode($json);
    }


    /**
     * Activa la reclasificación del tipo de evaluación cuando la etapa del proceso esta en la etapa de evaluación en adelante y la fase evaluación en adelante.
     * @autor: Sandra Saavedra
     * @param mixed $id_proceso_disciplinario
     *
     * @return json
     */
    public function activarRegistroFaseEvaluacionEtapaEvaluacion($id_proceso_disciplinario)
    {

        $fase_evaluacion = count(DB::select("select * from evaluacion where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "' and eliminado = 0"));

        if ($fase_evaluacion >= 2) {
            $reciboDatos['activar_funcionalidad'] = true;
        } else {
            $reciboDatos['activar_funcionalidad'] = false;
        }

        $json['data']['attributes'] = $reciboDatos;
        return json_encode($json);
    }



    /**
     * Validar si el proceso disciplinario ya ha sido validado en la fase validar_clasificacion.
     * @autor: Sandra Saavedra
     * @param mixed $id_proceso_disciplinario
     *
     * @return json
     */
    public function activarRegistroValidarClasificacionEtapaEvaluacion($id_proceso_disciplinario)
    {

        $fase_clasificacion = count(DB::select("select * from validar_clasificacion where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "'"));

        if ($fase_clasificacion > 0) {
            $reciboDatos['activar_funcionalidad'] = false;
        } else {
            $reciboDatos['activar_funcionalidad'] = true;
        }

        $json['data']['attributes'] = $reciboDatos;
        return json_encode($json);
    }



    /**
     *  Activa la fase de soportes de radicado cuando ya existe al menos un registrado interesado.
     * @autor: Sandra Saavedra
     * @param mixed $id_proceso_disciplinario
     *
     * @return json
     */
    public function activarSoporteRadicado($id_proceso_disciplinario)
    {

        $fase_datos_interesado = count(DB::select("select * from interesado where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "'"));

        if ($fase_datos_interesado > 0) {
            $reciboDatos['activar_funcionalidad'] = true;
            $json['data']['attributes'] = $reciboDatos;
            return json_encode($json);
        } else {
            $error['estado'] = false;
            $error['error'] = 'Se debe diligenciar un dato del interesado registrado en el sistema';
            return json_encode($error);
        }
    }


    /**
     * Activa la fase de soportes de radicado cuando ya existe al menos un registrado interesado.
     * @autor: Sandra Saavedra
     * @param mixed $id_proceso_disciplinario
     *
     * @return json
     */
    public function validarTipoExpedienteQuejaInterna($id_proceso_disciplinario)
    {

        $tipo_expediente = DB::select("select id_tipo_expediente, id_tipo_queja from clasificacion_radicado where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "'");

        if (count($tipo_expediente) > 0 && $tipo_expediente[0]->id_tipo_expediente ==  Constants::TIPO_EXPEDIENTE['queja'] &&  $tipo_expediente[0]->id_tipo_queja == Constants::TIPO_QUEJA['interna']) {
            $reciboDatos['queja_interna'] = true;
        } else {
            $reciboDatos['queja_interna'] = false;
        }

        $json['data']['attributes'] = $reciboDatos;
        return json_encode($json);
    }



    /**
     * Valida si el usuario que está en sesión es jefe de la dependencia que está en sesión.
     * @autor: Sandra Saavedra
     *
     * @return json
     */
    public function validarSiEsJefe()
    {

        $es_jefe = DB::select("
                select
                    name as nombre_funcionario
                from
                    users
                inner join mas_dependencia_origen on users.id = mas_dependencia_origen.id_usuario_jefe
                where mas_dependencia_origen.id = " . auth()->user()->id_dependencia . "
                and users.name = '" . auth()->user()->name . "'");

        if (count($es_jefe) > 0) {
            $reciboDatos['es_jefe'] = true;
        } else {
            $reciboDatos['es_jefe'] = false;
        }

        $json['data']['attributes'] = $reciboDatos;
        return json_encode($json);
    }



    /**
     * Se obtiene la información número radicado y vigencia de un proceso disciplinario.
     * @autor: Sandra Saavedra
     * @param mixed $id_proceso_disciplinario
     *
     * @return $json
     */
    public function getTituloProceso($id_proceso_disciplinario)
    {
        $respuesta = DB::select("
            select
            proceso_disciplinario.radicado as radicado,
            proceso_disciplinario.vigencia as vigencia,
            mas_tipo_proceso.nombre as nombre
            from proceso_disciplinario
            inner join mas_tipo_proceso on id_tipo_proceso = id
            where proceso_disciplinario.uuid = '" . $id_proceso_disciplinario . "'");

        if ($respuesta != null) {
            $datos['nombre'] = $respuesta[0]->nombre . " No. " . $respuesta[0]->radicado . " - " . $respuesta[0]->vigencia;
            $json['data']['attributes'] = $datos;
        }

        return $json;
    }


    /**
     * Se obtiene la etapa en la que se encuentra actualmente un proceso disciplinario.
     * @autor: Sandra Saavedra
     * @param mixed $id_proceso_disciplinario
     *
     * @return [type]
     */
    public function getEtapa($id_proceso_disciplinario)
    {
        $respuesta = DB::select("
            select
            id_etapa
            from proceso_disciplinario
            where uuid = '" . $id_proceso_disciplinario . "'");

        if ($respuesta != null) {
            $datos['etapa'] = $respuesta[0]->id_etapa;
            $json['data']['attributes'] = $datos;
        }

        return $json;
    }



    /**
     * @param mixed $id_proceso_disciplinario
     *
     * @return [type]
     */
    public function validarCrearClasificacion($id_proceso_disciplinario)
    {
        $respuesta = DB::select("
            select
            id_etapa
            from proceso_disciplinario
            where uuid = '" . $id_proceso_disciplinario . "'");

        if ($respuesta != null) {

            if ($respuesta[0]->id_etapa == Constants::ETAPA['captura_reparto']) {
                $datos['crear'] = true;
            } else {
                $datos['crear'] = false;
            }

            $json['data']['attributes'] = $datos;
        }

        return $json;
    }


    /**
     * Indica que el tipo de proceso es poder preferente
     * @autor: Sandra Saavedra
     * @param mixed $id_proceso_disciplinario
     *
     * @return json
     */
    public function ValidarTipoProceso($id_proceso_disciplinario)
    {

        $proceso_disciplinario = DB::select("select id_tipo_proceso from proceso_disciplinario
            where uuid = '" . $id_proceso_disciplinario . "'
            and (id_tipo_proceso = " . Constants::TIPO_DE_PROCESO['poder_preferente'] . " or id_tipo_proceso = " . Constants::TIPO_DE_PROCESO['poder_preferente'] . ")");

        if (!empty($proceso_disciplinario) && ($proceso_disciplinario[0]->id_tipo_proceso == Constants::TIPO_DE_PROCESO['poder_preferente'] || $proceso_disciplinario[0]->id_tipo_proceso == Constants::TIPO_DE_PROCESO['desglose'])) {
            $reciboDatos['tipo_proceso'] = false;
        } else {
            $reciboDatos['tipo_proceso'] = true;
        }

        $json['data']['attributes'] = $reciboDatos;
        return json_encode($json);
    }
}
