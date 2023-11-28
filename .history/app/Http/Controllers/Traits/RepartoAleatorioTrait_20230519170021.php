<?php

namespace App\Http\Controllers\Traits;

use App\Http\Utilidades\Constants;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Traits\NumeroCasosTrait;

trait RepartoAleatorioTrait
{
    use NumeroCasosTrait;

    public static function storeRepartoAleatorio($user_name, $id_proceso_disciplinario, $id_dependencia)
    {

        // se valida el tipo de expediente

        $tipo_expediente = DB::select("select id_tipo_expediente, id_tipo_queja, id_tipo_derecho_peticion, fecha_termino, hora_termino from clasificacion_radicado where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "' order by created_at desc");


        // se valida el tipo de expediente y se asigna el sub tipo de expediente
        if ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
            $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_derecho_peticion;
        }

        if ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
            $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
        }

        if ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja'] || $tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {
            $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
        }

        if ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario'] || $tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {
            $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
        }

        if ($tipo_expediente[0]->id_tipo_expediente ==  Constants::TIPO_EXPEDIENTE['tutela']) {

            if ($tipo_expediente[0]->fecha_termino != null) {

                $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['dias'];
            } else {

                $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['horas'];
            }
        }

        // Consulta que realiza el reparto aleatorio asignando los casos al que menos cantidad de procesos este gestionando.

        $aux = "
            SELECT
                te.user_id AS id_funcionario,
                u.name AS nombre_funcionario,
                te.tipo_expediente_id,
                te.sub_tipo_expediente_id,
                (select count(*) FROM log_proceso_disciplinario where id_funcionario_actual = u.name) AS num_casos_asginados,
                u.nivelacion AS nivelacion,
                (u.numero_casos+u.nivelacion) AS num_casos,
                u.numero_casos
            FROM
                users_tipo_expediente te
            INNER JOIN users u ON u.id = te.user_id
            INNER JOIN mas_dependencia_origen d ON d.id = u.id_dependencia
            WHERE te.tipo_expediente_id = " . $tipo_expediente[0]->id_tipo_expediente . "
            AND te.sub_tipo_expediente_id = " . $tipo_expediente[0]->sub_tipo_expediente_id . "
            AND d.id_usuario_jefe != u.id
            AND u.name != '" . $user_name . "'
            AND u.reparto_habilitado = 1
            AND u.estado = 1
            AND u.id_dependencia = " . $id_dependencia . "
            ORDER BY u.numero_casos ASC
        ";

        error_log($aux);

        $results = DB::select(
            "
            SELECT
                te.user_id AS id_funcionario,
                u.name AS nombre_funcionario,
                te.tipo_expediente_id,
                te.sub_tipo_expediente_id,
                (select count(*) FROM log_proceso_disciplinario where id_funcionario_actual = u.name) AS num_casos_asginados,
                u.nivelacion AS nivelacion,
                (u.numero_casos+u.nivelacion) AS num_casos,
                u.numero_casos
            FROM
                users_tipo_expediente te
            INNER JOIN users u ON u.id = te.user_id
            INNER JOIN mas_dependencia_origen d ON d.id = u.id_dependencia
            WHERE te.tipo_expediente_id = " . $tipo_expediente[0]->id_tipo_expediente . "
            AND te.sub_tipo_expediente_id = " . $tipo_expediente[0]->sub_tipo_expediente_id . "
            AND d.id_usuario_jefe != u.id
            AND u.name != '" . $user_name . "'
            AND u.reparto_habilitado = 1
            AND u.estado = 1
            AND u.id_dependencia = " . $id_dependencia . "
            ORDER BY u.numero_casos ASC"
        );

        error_log("Resultado: " . print_r($results, TRUE));

        if ($results != null) {
            error_log("ID_FUNCIONARIO: " . $results[0]->id_funcionario);
            error_log("NOMBRE_FUNCIONARIO: " . $results[0]->nombre_funcionario);
            error_log("NUM_CASOS: " . $results[0]->num_casos);
        }

        // RETORNA EL FUNCIONARIO ASIGNADO
        if ($results != null) {
            NumeroCasosTrait::numeroCasosUsuario($results[0]->nombre_funcionario, 1, true);
            return $results[0];
        } else {
            return null;
        }
    }





    public static function storeRepartoDirigidoValidarClasificacion($user_name, $id_clasificacion_radicado)
    {

        // error_log("select id_tipo_expediente, id_tipo_queja, id_tipo_derecho_peticion, fecha_termino, hora_termino
        // from clasificacion_radicado where uuid = '" . $id_clasificacion_radicado . "'");

        // se valida el tipo de expediente

        $tipo_expediente = DB::select("select id_tipo_expediente, id_tipo_queja, id_tipo_derecho_peticion, fecha_termino, hora_termino
            from clasificacion_radicado where uuid = '" . $id_clasificacion_radicado . "'");

        if ($tipo_expediente != null) {

            // se valida el tipo de expediente y se asigna el sub tipo de expediente
            if ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
                $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_derecho_peticion;
            } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
                $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
            } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja']) {
                $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
            } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']) {
                if ($tipo_expediente[0]->fecha_termino != null) {
                    $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['dias'];
                } else {
                    $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['horas'];
                }
            }

            $line = "select u.name as nombre_funcionario
            from users_tipo_expediente te
            inner join users u on u.id = te.user_id
            inner join mas_dependencia_origen d on d.id = u.id_dependencia
            where te.tipo_expediente_id = " . $tipo_expediente[0]->id_tipo_expediente . "
            and te.sub_tipo_expediente_id = " . $tipo_expediente[0]->sub_tipo_expediente_id . "
            and d.id_usuario_jefe <> u.id
            and u.name = '" . $user_name . "' and u.reparto_habilitado = 1 and u.estado = 1 and u.id_dependencia = " . auth()->user()->id_dependencia;
            // error_log($line);


            // Consulta que realiza el reparto aleatorio asignando los casos al que menos cantidad de procesos este gestionando.
            $results = DB::select("select u.name as nombre_funcionario
            from users_tipo_expediente te
            inner join users u on u.id = te.user_id
            inner join mas_dependencia_origen d on d.id = u.id_dependencia
            where te.tipo_expediente_id = " . $tipo_expediente[0]->id_tipo_expediente . "
            and te.sub_tipo_expediente_id = " . $tipo_expediente[0]->sub_tipo_expediente_id . "
            and d.id_usuario_jefe <> u.id
            and u.name = '" . $user_name . "' and u.reparto_habilitado = 1 and u.estado = 1 and u.id_dependencia = " . auth()->user()->id_dependencia);

            // RETORNA EL FUNCIONARIO ASIGNADO
            if ($results != null) {
                // error_log("NOMBRE_FUNCIONARIO: " . $results[0]->nombre_funcionario);
                return $results[0];
            }
        }

        return null;
    }

    public static function storeRepartoAleatorioPorGrupo($user_name, $id_dependencia, $id_grupoTrabajo)
    {
        // Consulta que realiza el reparto aleatorio asignando los casos al que menos cantidad de procesos este gestionando.
        $results = DB::select(
            "
            SELECT
                te.user_id AS id_funcionario,
                u.name AS nombre_funcionario,
                te.tipo_expediente_id,
                (select count(*) FROM log_proceso_disciplinario where id_funcionario_actual = u.name) AS num_casos_asginados,
                u.nivelacion AS nivelacion,
                (u.numero_casos+u.nivelacion) AS num_casos,
                u.numero_casos
            FROM
                users_tipo_expediente te
            INNER JOIN users u ON u.id = te.user_id
            INNER JOIN mas_dependencia_origen d ON d.id = u.id_dependencia
            WHERE d.id_usuario_jefe != u.id
            AND u.name != '" . $user_name . "'
            AND u.reparto_habilitado = 1
            AND u.estado = 1
            AND u.id_dependencia = " . $id_dependencia . "
            AND u.id_mas_grupo_trabajo_secretaria_comun like  '%" . $id_grupoTrabajo . "%'
            ORDER BY u.numero_casos ASC"
        );

        // RETORNA EL FUNCIONARIO ASIGNADO
        if ($results != null) {
            NumeroCasosTrait::numeroCasosUsuario($results[0]->nombre_funcionario, 1, true);
            return $results[0];
        } else {
            return null;
        }
    }
}
