<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\ClasificacionRadicado\ClasificacionRadicadoResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\ClasificacionRadicadoModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Support\Facades\DB;

trait ReclasificacionTrait
{

    /**
     *
     */
    public static function reclasificacionPorTipoExpediente($id_proceso_disciplinario)
    {

        DB::table('validar_clasificacion')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('evaluacion')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('remision_queja')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('remision_queja')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('gestor_respuesta')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('comunicacion_interesado')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('documento_cierre')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('informe_cierre')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('requerimiento_juzgado')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('cierre_etapa')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_etapa', Constants::ETAPA['evaluacion'])->update(['eliminado' => true]);

        DB::table('actuaciones')->where('uuid_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('proceso_disciplinario_por_semaforo')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('firma_actuaciones')->where('uuid_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);

        $existe_actuaciones = DB::table('actuaciones')->select('id_actuacion')->where('uuid_proceso_disciplinario', $id_proceso_disciplinario)->get();

        if (!empty($existe_actuaciones)) {
            DB::table('actuaciones_por_semaforo')->join('actuaciones a', 'a.uuid', '=', 'id_actuacion')->where('a.uuid_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
            DB::table('archivo_actuaciones')->join('actuaciones a', 'a.uuid', '=', 'uuid_actuacion')->where('a.uuid_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        }

        DB::table('proceso_disciplinario')->where('uuid', $id_proceso_disciplinario)->update(['id_etapa' => Constants::ETAPA['evaluacion']]);

        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['validacion_clasificacion'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['evaluacion'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['remision_queja'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['gestor_respuesta'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['comunicacion_interesado'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['documento_cierre'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['informe_cierre'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['requerimiento_juzgado'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['cierre_evaluacion'])->update(['eliminado' => true]);

        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['actuaciones_evaluacion_pd'])->update(['eliminado' => true]);
    }

    /**
     *
     */
    public static function reclasificacionPorTipoEvaluacion($id_proceso_disciplinario)
    {
        DB::table('evaluacion')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('remision_queja')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('remision_queja')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('gestor_respuesta')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('comunicacion_interesado')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('documento_cierre')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('informe_cierre')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('requerimiento_juzgado')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('cierre_etapa')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_etapa', Constants::ETAPA['evaluacion'])->update(['eliminado' => true]);

        DB::table('actuaciones')->where('uuid_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('proceso_disciplinario_por_semaforo')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        DB::table('firma_actuaciones')->where('uuid_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);

        $existe_actuaciones = DB::table('actuaciones')->select('id_actuacion')->where('uuid_proceso_disciplinario', $id_proceso_disciplinario)->get();

        error_log("EXISTE ACTUACION" . $existe_actuaciones);

        if (!empty($existe_actuaciones)) {
            DB::table('actuaciones_por_semaforo')->join('actuaciones a', 'a.uuid', '=', 'id_actuacion')->where('a.uuid_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
            DB::table('archivo_actuaciones')->join('actuaciones a', 'a.uuid', '=', 'uuid_actuacion')->where('a.uuid_proceso_disciplinario', $id_proceso_disciplinario)->update(['eliminado' => true]);
        }

        DB::table('proceso_disciplinario')->where('uuid', $id_proceso_disciplinario)->update(['id_etapa' => Constants::ETAPA['evaluacion']]);

        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['evaluacion'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['remision_queja'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['gestor_respuesta'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['comunicacion_interesado'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['documento_cierre'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['informe_cierre'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['requerimiento_juzgado'])->update(['eliminado' => true]);
        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['cierre_evaluacion'])->update(['eliminado' => true]);

        DB::table('log_proceso_disciplinario')->where('id_proceso_disciplinario', $id_proceso_disciplinario)->where('id_fase', Constants::FASE['actuaciones_evaluacion_pd'])->update(['eliminado' => true]);
    }


    /**
     *
     */
    public static function reclasificacionProcesoDisciplinario($id_proceso_disciplinario)
    {

        $clasificacion_radicado = new RepositoryGeneric();
        $clasificacion_radicado->setModel(new ClasificacionRadicadoModel());
        $query = $clasificacion_radicado->customQuery(function ($model) use ($id_proceso_disciplinario) {
            return
                $model->where('id_proceso_disciplinario', $id_proceso_disciplinario)
                ->where('estado', true)
                ->get();
        });

        // error_log("ID_PROCESO_DISCIPLINARIO ".$query[0]->id_estado_reparto);

        $clasificado['id_proceso_disciplinario'] = $id_proceso_disciplinario;
        $clasificado['id_etapa'] = LogTrait::etapaActual($id_proceso_disciplinario);
        $clasificado['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['proceso_disciplinario'];
        $clasificado['observaciones'] = "Pasa a hacer proceso disciplinario";
        $clasificado['id_tipo_queja'] = $query[0]->id_tipo_queja;
        $clasificado['id_termino_respuesta'] = null;
        $clasificado['fecha_termino'] = null;
        $clasificado['hora_termino'] = null;
        $clasificado['gestion_juridica'] = null;
        $clasificado['estado'] = true;
        $clasificado['id_estado_reparto'] = $query[0]->id_estado_reparto;
        $clasificado['oficina_control_interno'] = null;
        $clasificado['id_tipo_derecho_peticion'] = null;
        $clasificado['created_user'] = auth()->user()->name;
        $clasificado['per_page'] = $query[0]->per_page;
        $clasificado['current_page'] = $query[0]->current_page;
        $clasificado['reclasificacion'] = null;
        $clasificado['reparto'] = $query[0]->reparto;
        $clasificado['id_dependencia'] = auth()->user()->id_dependencia;
        $clasificado['validacion_jefe'] = null;
        $clasificado['id_fase'] = Constants::FASE['cierre_evaluacion'];

        // ACTUALIZA TODOS EL HISTORIAL EN INACTIVO PARA DEJAR SOLAMENTE EL ÚLTIMO COMO ACTIVO.
        ClasificacionRadicadoModel::where('estado', 1)->where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['estado' => 0]);

        $clasificacionModel = new ClasificacionRadicadoModel();
        ClasificacionRadicadoResource::make($clasificacionModel->create($clasificado));

        //registramos log
        $logRequest['id_proceso_disciplinario'] = $id_proceso_disciplinario;
        $logRequest['id_etapa'] = LogTrait::etapaActual($id_proceso_disciplinario);
        $logRequest['id_fase'] = Constants::FASE['cierre_evaluacion'];
        $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
        $logRequest['descripcion'] = 'Pasa a hacer proceso disciplinario';
        $logRequest['created_user'] = auth()->user()->name;
        $logRequest['id_estado'] = 3; // Remisionado
        $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia; // Remisionado
        $logRequest['documentos'] = false;
        $logRequest['id_fase_registro'] = $query[0]->uuid;
        $logRequest['id_funcionario_actual'] = null;
        $logRequest['id_funcionario_registra'] = auth()->user()->name;
        $logRequest['id_funcionario_asignado'] = auth()->user()->name;
        $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['ninguno'];

        $logModel = new LogProcesoDisciplinarioModel();
        LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
    }
}
