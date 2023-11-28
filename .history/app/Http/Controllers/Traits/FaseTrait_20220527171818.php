<?php

namespace App\Http\Controllers\Traits;


use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use Illuminate\Support\Facades\DB;

trait FaseTrait
{
    /**
     * Se guarda en el log el registro de la clasificación del expediente.
     *
     *
     */
    public static function storeLogClasificacionExpediente($datosRequest, $dependencia, $funcionario_asignado, $id_clasificacion_radicado, $reclasificacion)
    {

        if($reclasificacion){
            DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null, 'id_estado' => Constants::ESTADO_PROCESO_DISCIPLINARIO['contestado']]);
            $logRequest['id_funcionario_actual'] = $funcionario_asignado;
        }
        else{
            $logRequest['id_funcionario_actual'] = null;
        }

        // LOG PROCESO DISCIPLINARIO
        $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
        $logRequest['id_etapa'] =  $datosRequest['id_etapa'];
        $logRequest['id_fase'] = $datosRequest['id_fase'];
        $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
        $logRequest['id_estado'] = Constants::ESTADO_PROCESO_DISCIPLINARIO['remitido'];
        $logRequest['descripcion'] = $datosRequest['observaciones'];
        $logRequest['id_dependencia_origen'] = $dependencia;
        $logRequest['id_funcionario_registra'] = $datosRequest['created_user'];
        $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['reclasificacion_expediente'];
        $logRequest['id_fase_registro'] = $id_clasificacion_radicado;
        $logRequest['id_funcionario_asignado'] = $funcionario_asignado;

        $logModel = new LogProcesoDisciplinarioModel();
        return LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
    }


    /**
     *
     */
    public static function removerFuncionarioActualLog($id_proceso_disciplinario){

        DB::table('log_proceso_disciplinario')
        ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
        ->update(['id_funcionario_actual' => null, 'id_estado' => Constants::ESTADO_PROCESO_DISCIPLINARIO['contestado']]);

    }





}

