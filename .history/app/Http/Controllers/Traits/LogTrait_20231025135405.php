<?php

namespace App\Http\Controllers\Traits;


use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use Illuminate\Support\Facades\DB;

trait LogTrait
{
    /**
     * Se guarda en el log el registro de la clasificación del expediente.
     *
     *
     */
    public static function storeLogClasificacionExpediente($datosRequest, $dependencia, $funcionario_asignado, $id_clasificacion_radicado, $reclasificacion)
    {

        if ($reclasificacion) {
            DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null, 'id_estado' => Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['contestado']]);
            $logRequest['id_funcionario_actual'] = $funcionario_asignado;
        } else {
            $logRequest['id_funcionario_actual'] = null;
        }

        // LOG PROCESO DISCIPLINARIO
        $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
        $logRequest['id_etapa'] = $datosRequest['id_etapa'];
        $logRequest['id_fase'] = $datosRequest['id_fase'];
        $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
        $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
        $logRequest['descripcion'] = $datosRequest['observaciones'];
        $logRequest['id_dependencia_origen'] = $dependencia;
        $logRequest['id_tipo_transaccion'] = $datosRequest['id_tipo_transaccion'];
        $logRequest['created_user'] = auth()->user()->name;
        $logRequest['id_fase_registro'] = $id_clasificacion_radicado;
        $logRequest['id_funcionario_registra'] = $datosRequest['created_user'];
        $logRequest['id_funcionario_asignado'] = $funcionario_asignado;
        $logRequest['eliminado'] = false;

        $logModel = new LogProcesoDisciplinarioModel();
        return LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
    }


    /**
     *
     */
    public static function removerFuncionarioActualLog($id_proceso_disciplinario)
    {

        DB::table('log_proceso_disciplinario')
            ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
            ->update(['id_funcionario_actual' => null, 'id_estado' => Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['contestado']]);
    }


    /**
     * Trae la ultima etapa actualiozada en el sistema
     */
    public static function etapaActual($id_proceso_disciplinario)
    {

        $query = DB::select("
            select
                id_etapa from proceso_disciplinario
            where
                uuid = '" . $id_proceso_disciplinario . "'");

        $aux = "select id_etapa from proceso_disciplinario where uuid = '" . $id_proceso_disciplinario . "'";

        // error_log($aux);

        return $query[0]->id_etapa;
    }

    /**
     *
     */
    public static function storeLog($datosRequest, $dependencia, $funcionario_asignado, $id_tabla_fase_registro, $tipo_transaccion)
    {

        DB::table('log_proceso_disciplinario')
            ->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])
            ->update(['id_funcionario_actual' => null, 'id_estado' => Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['contestado']]);

        // LOG PROCESO DISCIPLINARIO
        $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
        $logRequest['id_etapa'] = $datosRequest['id_etapa'];
        $logRequest['id_fase'] = $datosRequest['id_fase'];
        $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
        $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['contestado'];
        $logRequest['descripcion'] = $datosRequest['observaciones'];
        $logRequest['id_dependencia_origen'] = $dependencia;
        $logRequest['id_tipo_transaccion'] = $tipo_transaccion;
        $logRequest['created_user'] = auth()->user()->name;
        $logRequest['id_fase_registro'] = $id_tabla_fase_registro;
        $logRequest['id_funcionario_registra'] = $datosRequest['created_user'];
        $logRequest['id_funcionario_asignado'] = $funcionario_asignado;
        $logRequest['id_funcionario_actual'] = $funcionario_asignado;

        $logModel = new LogProcesoDisciplinarioModel();
        return LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
    }


    /**
     *
     */
    public static function storeLogMigracion($id_etapa, $id_proceso_disciplinario, $id_dependencia, $id_fase, $id_tabla_fase_registro, $usuario_actual, $fecha_registro)
    {

        LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['id_funcionario_actual' => null]);

        // LOG PROCESO DISCIPLINARIO
        $logRequest['id_proceso_disciplinario'] = $id_proceso_disciplinario;
        $logRequest['id_etapa'] = $id_etapa;
        $logRequest['id_fase'] = $id_fase;
        $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
        $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['contestado'];
        $logRequest['descripcion'] = "migracion";
        $logRequest['id_dependencia_origen'] = $id_dependencia;
        $logRequest['id_tipo_transaccion'] = 7;
        $logRequest['created_user'] = auth()->user()->name;
        $logRequest['created_at'] = $fecha_registro;
        $logRequest['id_fase_registro'] = $id_tabla_fase_registro;
        $logRequest['id_funcionario_registra'] = auth()->user()->name;
        $logRequest['id_funcionario_asignado'] = $usuario_actual;
        $logRequest['id_funcionario_actual'] = $usuario_actual;
        $logRequest['eliminado'] = false;

        $logModel = new LogProcesoDisciplinarioModel();
        return LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
    }


    /**
     * SOLO DEBE SER USADO EN AMBIENTE DE PRUEBAS
     */
    public static function getDependenciaAmbientePruebas($dependencia)
    {

        if ($dependencia == 310) { //SECRETARIA COMUN
            return 413;
        } else if ($dependencia == 64) { //PD PARA LA POTESTAD DISCIPLINARIA I FORSECURITY
            return 414;
        } else if ($dependencia == 31) { //PD PARA LA POTESTAD DISCIPLINARIA II FORSECURITY
            return 415;
        } else if ($dependencia == 5) { //PD PARA LA POTESTAD DISCIPLINARIA III FORSECURITY
            return 416;
        } else if ($dependencia == 61) { //PD PARA LA POTESTAD DISCIPLINARIA IV FORSECURITY
            return 417;
        } else if ($dependencia == 392) { //CONTROL INTERNO DISCIPLINARIO FORSECURITY
            return 418;
        } else {
            return 414;
        }

        return $dependencia;
    }
}
