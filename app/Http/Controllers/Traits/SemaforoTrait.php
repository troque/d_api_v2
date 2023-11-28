<?php

namespace App\Http\Controllers\Traits;

use App\Http\Utilidades\Constants;
use App\Models\ActuacionesModel;
use App\Models\ActuacionInactivaModel;
use App\Models\MasActuacionesModel;
use App\Models\PortalNotificacionesLogModel;
use App\Models\PortalNotificacionesModel;
use App\Models\ProcesoDisciplinarioPorSemaforoModel;
use App\Models\SemaforoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Support\Facades\DB;
use stdClass;

trait SemaforoTrait
{

    public static function obtenerSemaforosActuacionesPorEtapaYMasActuacion($id_mas_actuacion, $id_etapa)
    {
        $query = DB::select(
            "
                SELECT
                    pdps.id
                FROM
                    proceso_disciplinario_por_semaforo pdps
                INNER JOIN semaforo s ON pdps.id_semaforo = s.id
                INNER JOIN auto_finaliza af ON s.id = af.id_semaforo
                WHERE pdps.id_proceso_disciplinario = '9d0040bc-e3fd-4933-a3fc-e374427c5561'
                AND af.id_etapa = $id_etapa
                AND af.id_mas_actuacion = $id_mas_actuacion
            "
        );

        return $query;
    }

    public static function finalizarSemaforo($id_semaforo_proceso_disciplinario, $id_actuacion_finaliza, $id_dependencia_finaliza, $id_usuario_finaliza)
    {
        /*ProcesoDisciplinarioPorSemaforoModel::where('id_semaforo', $id_semaforo)
            ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
            ->whereNull('finalizo')
            ->update(['finalizo' => "si", 'fechafinalizo' => date('Y-m-d h:i:s')]);

        if($id_actuacion_finaliza){
            ProcesoDisciplinarioPorSemaforoModel::where('id_semaforo', $id_semaforo)
            ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
            ->whereNull('id_actuacion_finaliza')
            ->update(['id_actuacion_finaliza' => $id_actuacion_finaliza]);
        }
        if($id_dependencia_finaliza){
            ProcesoDisciplinarioPorSemaforoModel::where('id_semaforo', $id_semaforo)
            ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
            ->whereNull('id_dependencia_finaliza')
            ->update(['id_dependencia_finaliza' => $id_dependencia_finaliza]);
        }
        if($id_usuario_finaliza){
            ProcesoDisciplinarioPorSemaforoModel::where('id_semaforo', $id_semaforo)
            ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
            ->whereNull('id_usuario_finaliza')
            ->update(['id_usuario_finaliza' => $id_usuario_finaliza]);
        }*/

        $query1 = ProcesoDisciplinarioPorSemaforoModel::where('id', $id_semaforo_proceso_disciplinario)
            ->update(['finalizo' => "si", 'fechafinalizo' => date('Y-m-d h:i:s')]);
        $query2 = '';
        $query3 = '';
        $query4 = '';

        if($id_actuacion_finaliza){
            $query2 = ProcesoDisciplinarioPorSemaforoModel::where('id', $id_semaforo_proceso_disciplinario)
            ->update(['id_actuacion_finaliza' => $id_actuacion_finaliza]);
        }
        if($id_dependencia_finaliza){
            $query3 = ProcesoDisciplinarioPorSemaforoModel::where('id', $id_semaforo_proceso_disciplinario)
            ->update(['id_dependencia_finaliza' => $id_dependencia_finaliza]);
        }
        if($id_usuario_finaliza){
            $query4 = ProcesoDisciplinarioPorSemaforoModel::where('id', $id_semaforo_proceso_disciplinario)
            ->update(['id_usuario_finaliza' => $id_usuario_finaliza]);
        }

        //dd($query1, $query2, $query3, $query4);

    }

    public static function obtenerSemaforosQueInicianActuacion($id_mas_actuacion, $id_etapa)
    {
        $query = DB::select(
            "
                SELECT
                    s.id
                FROM
                    semaforo s
                WHERE s.estado = 1
                AND s.id_etapa = $id_etapa
                AND s.id_mas_actuacion_inicia = $id_mas_actuacion
            "
        );

        return $query;
    }

    public static function iniciarSemaforo($semaforo)
    {
        $query = ProcesoDisciplinarioPorSemaforoModel::create($semaforo);
        //dd($query);
        return $query;
    }

    public static function anularActuaciones($uuidActuacion)
    {
        //INACTIVAR ACTUACIONES EN CASO DE QUE APLIQUE
        $cont = 0;
        $actuaciones = ActuacionesModel::where('UUID', $uuidActuacion)->get();
        $mas_actuaciones = MasActuacionesModel::where('id', $actuaciones[0]->id_actuacion)->first();
        if($mas_actuaciones->despues_aprobacion_listar_actuacion == true){
            $actuacionInactivaModel = ActuacionInactivaModel::where('id_actuacion_principal', $uuidActuacion)->get();
            foreach($actuacionInactivaModel as $inactivos){
                $cont++;
                ActuacionesModel::where('UUID', $inactivos->id_actuacion)->where('estado', 1)->update(['estado' => 0, 'id_estado_actuacion' => Constants::ESTADOS_ACTUACION['actuacion_inactivada']]);

                $notificaciones = PortalNotificacionesModel::where('id_actuacion', $inactivos->id_actuacion)->get();

                PortalNotificacionesModel::where('id_actuacion', $inactivos->id_actuacion)->where('estado', 1)->update(['estado' => 0]);

                foreach($notificaciones as $notificacion){
                    $portalDocumentoNotificacionesLog['id_notificacion'] = $notificacion->uuid;
                    $portalDocumentoNotificacionesLog['id_dependencia'] = auth()->user()->id_dependencia;
                    $portalDocumentoNotificacionesLog['created_user'] = auth()->user()->name;
                    $portalDocumentoNotificacionesLog['descripcion'] = 'LA NOTIFICACIÓN HA SIDO INACTIVADA DEBIDO A UNA ACTUACIÓN DE ANULACIÓN (.' . $mas_actuaciones->nombre_actuacion . ')';    
                    PortalNotificacionesLogModel::create($portalDocumentoNotificacionesLog);
                }
            }
        }        

    }
}
