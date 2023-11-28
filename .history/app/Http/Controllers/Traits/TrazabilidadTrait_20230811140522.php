<?php

namespace App\Http\Controllers\Traits;

use App\Http\Controllers\Api\ProcesoDiciplinarioController;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\ProcesoDisciplinarioPorSemaforoModel;
use App\Models\SemaforoModel;
use App\Models\User;
use App\Repositories\RepositoryGeneric;
use Illuminate\Support\Facades\DB;
use stdClass;

trait TrazabilidadTrait
{

    public function getUsuarioCreadorActuacion($idActuacion, $id_proceso_disciplinario)
    {

        $proceso_disciplinario = DB::select(
            "
                SELECT
                    cr.id_tipo_expediente,
                    cr.id_tipo_queja,
                    cr.created_at
                FROM
                proceso_disciplinario pd
                INNER JOIN clasificacion_radicado cr ON pd.uuid = cr.id_proceso_disciplinario
                WHERE pd.uuid = '$id_proceso_disciplinario'
                ORDER BY cr.created_at DESC
            "
        );

        if (count($proceso_disciplinario) <= 0) {
            $consulta = new stdClass;
            $consulta->error = true;
            $consulta->mensaje = 'DATOS DEL PROCESO DISCIPLINARIO NO ENCONTRADOS';
            $consulta->resultado = [];
            return $consulta;
        }

        $query = DB::select(
            "
                SELECT
                    ta.id_dependencia,
                    ta.created_user
                FROM
                    trazabilidad_actuaciones ta
                WHERE ta.uuid_actuacion = '$idActuacion'
                ORDER BY ta.created_at ASC
            "
        );

        $user = DB::select(
            "
                SELECT
                    u.id,
                    u.name,
                    u.nombre,
                    u.apellido,
                    u.id_dependencia,
                    u.reparto_habilitado,
                    u.estado,
                    ute.tipo_expediente_id,
                    ute.sub_tipo_expediente_id
                FROM
                    users u
                INNER JOIN users_tipo_expediente ute ON u.id = ute.user_id
                WHERE u.name = '" . $query[0]->created_user . "'
                AND u.reparto_habilitado = " . Constants::ESTADOS['activo'] . "
                AND u.estado = " . Constants::ESTADOS['activo'] . "
                AND ute.tipo_expediente_id = " . Constants::TIPO_EXPEDIENTE['proceso_disciplinario'] . "
                AND ute.sub_tipo_expediente_id = " . $proceso_disciplinario[0]->id_tipo_queja . "
                AND u.id_dependencia = " . $query[0]->id_dependencia . "
            "
        );

        return $user;
    }

    public static function enviarProcesoDisciplinarioAUsuario($datosRecibidos)
    {

        //generamos log
        $logRequest['id_proceso_disciplinario'] = $datosRecibidos['id_proceso_disciplinario'];
        $logRequest['id_etapa'] = $datosRecibidos['id_etapa'];
        $logRequest['id_fase'] =  Constants::FASE['transacciones'];
        $logRequest['id_tipo_log'] = 1; // Log de tipo Etapa
        $logRequest['documentos'] = false;
        $logRequest['descripcion'] = substr($datosRecibidos['descripcion_a_remitir'], 0, 4000);
        $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
        $logRequest['id_dependencia_origen'] = $datosRecibidos['id_dependencia_origen'];
        $logRequest['id_funcionario_actual'] = $datosRecibidos['usuario_a_remitir'];
        $logRequest['id_funcionario_registra'] = auth()->user()->name;
        $logRequest['id_funcionario_asignado'] = $datosRecibidos['usuario_a_remitir'];
        $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['reasignacion'];
        ProcesoDiciplinarioController::removerFuncionarioActualLog($datosRecibidos['id_proceso_disciplinario']);

        $logModel = new LogProcesoDisciplinarioModel();
        $respuesta = LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

        $userDestino = User::where('name', $datosRecibidos['usuario_a_remitir'])->get();
        //dd($userDestino);

        if (count($userDestino) <= 0) {
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  "USUARIO DESTINO NO ENCONTRADO"
            ), 500);
        }

        $procesoDisciplinario = ProcesoDiciplinarioModel::where('uuid', $datosRecibidos['id_proceso_disciplinario'])->update(['id_dependencia_actual' => $userDestino[0]->id_dependencia]);

        return $respuesta;
    }
}
