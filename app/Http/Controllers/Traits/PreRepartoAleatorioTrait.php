<?php

namespace App\Http\Controllers\Traits;


use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use Illuminate\Support\Facades\DB;
use stdClass;

trait PreRepartoAleatorioTrait
{
    public function preRepartoAleatorio($id_proceso_disciplinario, $id_funcionalidad, $busqueda_usuario){
        try {
            if($busqueda_usuario == null){
                // REPARTO Y BALANCEO DE CARGA
                // Se valida la dependencia en la que se encuentra el usuario en sesión
                $dependencia = DB::select("
                    SELECT
                    id_dependencia
                    FROM USERS
                    WHERE NAME = '".auth()->user()->name."'
                ");

                // se valida el tipo de expediente
                $tipo_expediente = DB::select("
                    SELECT
                        id_tipo_expediente,
                        id_tipo_queja,
                        id_tipo_derecho_peticion,
                        fecha_termino,
                        hora_termino
                    FROM
                        clasificacion_radicado
                    WHERE id_proceso_disciplinario = '".$id_proceso_disciplinario."'
                    ORDER BY created_at DESC
                ");

                // se valida el tipo de expediente y se asigna el sub tipo de expediente
                if($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']){
                    $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_derecho_peticion;
                }
                elseif($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']){
                    $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
                }
                elseif($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja']){
                    $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
                }
                elseif($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']){
                    if($tipo_expediente[0]->fecha_termino!=null){
                        $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['dias'];
                    }
                    else{
                        $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['horas'];
                    }
                }
                //error_log("TIPO DE SUB_EXPEDIENTE: ".$tipo_expediente[0]->sub_tipo_expediente_id);

                // Consulta que realiza el reparto aleatorio asignando los casos al que menos cantidad de procesos este gestionando.
                $results = DB::select("
                    SELECT
                        te.user_id AS id_funcionario,
                        u.NAME AS nombre_funcionario,
                        u.NOMBRE AS nombre_completo,
                        u.APELLIDO AS apellido_completo,
                        (select count(*) FROM log_proceso_disciplinario where id_funcionario_actual = u.name) AS num_casos_asginados,
                        u.nivelacion AS nivelacion,
                        (u.numero_casos+u.nivelacion) AS num_casos,
                        u.numero_casos
                    FROM users_tipo_expediente te
                    INNER JOIN users u ON u.ID = te.user_id
                    INNER JOIN users_roles ur ON u.id = ur.user_id
                    INNER JOIN funcionalidad_rol fr ON fr.role_id = ur.role_id
                    WHERE te.tipo_expediente_id = ".$tipo_expediente[0]->id_tipo_expediente."
                    AND te.sub_tipo_expediente_id = ".$tipo_expediente[0]->sub_tipo_expediente_id."
                    AND u.reparto_habilitado = 1
                    AND u.estado = 1
                    AND u.id_dependencia = ".$dependencia[0]->id_dependencia."
                    AND fr.funcionalidad_id = ".$id_funcionalidad."
                    AND u.name = (
                        SELECT
                            u2.name
                        FROM users u2
                        INNER JOIN users_roles ur2 ON u2.id = ur2.user_id
                        INNER JOIN funcionalidad_rol fr2 ON fr2.role_id = ur2.role_id
                        INNER JOIN mas_funcionalidad mf2 ON fr2.funcionalidad_id = mf2.id
                        INNER JOIN mas_modulo mm2 ON mf2.id_modulo = mm2.id
                        WHERE u2.id = u.id
                        AND u2.reparto_habilitado = u.reparto_habilitado
                        AND u2.estado = u.estado
                        AND u2.id_dependencia = u.id_dependencia
                        AND mm2.nombre = 'E_GestorRespuesta'
                        AND mf2.nombre = 'Crear'
                        GROUP BY u2.name
                    )
                    ORDER BY u.numero_casos ASC
                ");

                if(count($results) <= 0){
                    $error = new stdClass;
                    $error->estado = false;
                    $error->error = 'No existen funcionarios con permisos para gestionar este tipo de expediente.';
                    return $error;
                }

                $results[0]->id_dependencia_origen = $dependencia[0]->id_dependencia;
                $results[0]->id_funcionario_actual = $results[0]->nombre_funcionario;
                $results[0]->id_funcionario_asignado = $results[0]->nombre_funcionario;
                $results[0]->estado = true;
            }
            else{
                $results = DB::select("
                    SELECT
                        te.user_id AS id_funcionario,
                        u.NAME AS nombre_funcionario,
                        u.NOMBRE AS nombre_completo,
                        u.APELLIDO AS apellido_completo,
                        u.id_dependencia,
                        (select count(*) FROM log_proceso_disciplinario where id_funcionario_actual = u.name) AS num_casos_asginados,
                        u.nivelacion AS nivelacion,
                        (u.numero_casos+u.nivelacion) AS num_casos,
                        u.numero_casos
                    FROM users_tipo_expediente te
                    INNER JOIN users u ON u.ID = te.user_id
                    WHERE te.tipo_expediente_id = 3
                    AND u.reparto_habilitado = 1
                    AND u.estado = 1
                    AND u.NAME = '".$busqueda_usuario."'
                    ORDER BY u.numero_casos ASC
                ");

                /**REPARTO TEMPORAL, MODIFICAR DE ACUERDO AL USUARIO IDEAL LUEGO DE QUE SE ACLAREN LAS DUDAS */
                if(count($results) <= 0){

                    // REPARTO Y BALANCEO DE CARGA
                    // Se valida la dependencia en la que se encuentra el usuario en sesión
                    $dependencia = DB::select("
                        SELECT
                        id_dependencia
                        FROM USERS
                        WHERE NAME = '".auth()->user()->name."'
                    ");

                    // se valida el tipo de expediente
                    $tipo_expediente = DB::select("
                        SELECT
                            id_tipo_expediente,
                            id_tipo_queja,
                            id_tipo_derecho_peticion,
                            fecha_termino,
                            hora_termino
                        FROM
                            clasificacion_radicado
                        WHERE id_proceso_disciplinario = '".$id_proceso_disciplinario."'
                        ORDER BY created_at DESC
                    ");

                    // se valida el tipo de expediente y se asigna el sub tipo de expediente
                    if($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']){
                        $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_derecho_peticion;
                    }
                    elseif($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']){
                        $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
                    }
                    elseif($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja']){
                        $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
                    }
                    elseif($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']){
                        if($tipo_expediente[0]->fecha_termino!=null){
                            $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['dias'];
                        }
                        else{
                            $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['horas'];
                        }
                    }

                    $results = DB::select("
                        SELECT
                            te.user_id AS id_funcionario,
                            u.NAME AS nombre_funcionario,
                            u.NOMBRE AS nombre_completo,
                            u.APELLIDO AS apellido_completo,
                            (select count(*) FROM log_proceso_disciplinario where id_funcionario_actual = u.name) AS num_casos_asginados,
                            u.nivelacion AS nivelacion,
                            (u.numero_casos+u.nivelacion) AS num_casos,
                            u.numero_casos
                        FROM users_tipo_expediente te
                        INNER JOIN users u ON u.ID = te.user_id
                        INNER JOIN users_roles ur ON u.id = ur.user_id
                        INNER JOIN funcionalidad_rol fr ON fr.role_id = ur.role_id
                        INNER JOIN mas_funcionalidad mf ON fr.funcionalidad_id = mf.id
                        INNER JOIN mas_modulo mm ON mf.id_modulo = mm.id
                        WHERE te.tipo_expediente_id = ".$tipo_expediente[0]->id_tipo_expediente."
                        AND te.sub_tipo_expediente_id = ".$tipo_expediente[0]->sub_tipo_expediente_id."
                        AND u.reparto_habilitado = 1
                        AND u.estado = 1
                        AND u.id_dependencia = ".$dependencia[0]->id_dependencia."
                        AND mm.nombre = 'E_GestorRespuesta'
                        AND mf.nombre = 'Crear'
                        ORDER BY u.numero_casos ASC
                    ");

                    if(count($results) <= 0){
                        $error = new stdClass;
                        $error->estado = false;
                        $error->error = 'No existen funcionarios con permisos para gestionar este tipo de expediente.';
                        return $error;
                    }

                    $results[0]->id_dependencia = $dependencia[0]->id_dependencia;

                }
                /**REPARTO TEMPORAL, MODIFICAR DE ACUERDO AL USUARIO IDEAL LUEGO DE QUE SE ACLAREN LAS DUDAS */

                $results[0]->id_dependencia_origen = $results[0]->id_dependencia;
                $results[0]->id_funcionario_actual = $results[0]->nombre_funcionario;
                $results[0]->id_funcionario_asignado = $results[0]->nombre_funcionario;
                $results[0]->estado = true;
            }

            return $results[0];

        } catch (\Exception $e) {
            error_log($e);
            dd($e);
            DB::connection()->rollBack();
            return $e;
            if (empty($results)) {
                $error['estado'] = false;
                $error['error'] = 'No existen funcionarios con permisos para gestionar este tipo de expediente.';
                return json_encode($error);
            }
        }
    }
}

