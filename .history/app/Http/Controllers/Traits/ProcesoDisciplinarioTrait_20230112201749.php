<?php

namespace App\Http\Controllers\Traits;

use App\Http\Utilidades\Constants;
use App\Models\EvaluacionModel;
use Error;
use Illuminate\Support\Facades\DB;

trait ProcesoDisciplinarioTrait
{

    /**
     * Sube y guarda la documentación
     */
    public static function obtenerDatosProcesoDisciplinario($id_proceso_disciplinario)
    {
        try {
            // se valida el tipo de expediente
            $tipo_expediente = DB::select("
                SELECT
                    cr.id_tipo_expediente,
                    cr.id_tipo_queja,
                    cr.id_tipo_derecho_peticion,
                    cr.fecha_termino,
                    cr.hora_termino,
                    cr.gestion_juridica,
                    mte.nombre
                FROM
                    clasificacion_radicado cr
                INNER JOIN mas_tipo_expediente mte ON cr.id_tipo_expediente = mte.id
                WHERE cr.id_proceso_disciplinario = '" . $id_proceso_disciplinario . "'
                AND cr.estado = " . Constants::ESTADOS['activo'] . "
                ORDER BY cr.created_at DESC
            ");

            $aux = "SELECT
                    cr.id_tipo_expediente,
                    cr.id_tipo_queja,
                    cr.id_tipo_derecho_peticion,
                    cr.fecha_termino,
                    cr.hora_termino,
                    cr.gestion_juridica,
                    mte.nombre
                FROM
                    clasificacion_radicado cr
                INNER JOIN mas_tipo_expediente mte ON cr.id_tipo_expediente = mte.id
                WHERE cr.id_proceso_disciplinario = '" . $id_proceso_disciplinario . "'
                AND cr.estado = " . Constants::ESTADOS['activo'] . "
                ORDER BY cr.created_at DESC";

            error_log($aux);

            // se valida el tipo de expediente y se asigna el sub tipo de expediente
            if ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
                $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_derecho_peticion;

                $derecho_peticion = DB::select(
                    "
                    SELECT
                        mtdp.id,
                        mtdp.nombre
                    FROM
                        mas_tipo_derecho_peticion mtdp
                    WHERE mtdp.id = " . $tipo_expediente[0]->sub_tipo_expediente_id
                );

                $tipo_expediente[0]->sub_nombre = $derecho_peticion[0]->nombre;
            } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
                $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
            } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja'] || $tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {
                $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;

                $queja = DB::select(
                    "
                    SELECT
                        mtq.id,
                        mtq.nombre
                    FROM
                        mas_tipo_queja mtq
                    WHERE mtq.id = " . $tipo_expediente[0]->sub_tipo_expediente_id
                );

                $tipo_expediente[0]->sub_nombre = $queja[0]->nombre;
            } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']) {
                if ($tipo_expediente[0]->fecha_termino != null) {
                    $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['dias'];
                    $tipo_expediente[0]->sub_nombre = 'Dias';
                } else {
                    $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['horas'];
                    $tipo_expediente[0]->sub_nombre = 'horas';
                }
            }

            $tipo_expediente[0]->id_tercer_expediente = $tipo_expediente[0]->gestion_juridica;

            $tipo_evaluacion = EvaluacionModel::where('id_proceso_disciplinario', $id_proceso_disciplinario)->latest('created_at')->get();

            if (count($tipo_evaluacion) > 0) {
                $tipo_expediente[0]->id_evaluacion = $tipo_evaluacion[0]->resultado_evaluacion;
            } else {
                $tipo_expediente[0]->id_evaluacion = 0;
            }

            return $tipo_expediente[0];
        } catch (\Exception $e) {
            error_log($e);
            return $e;
        }
    }
}
