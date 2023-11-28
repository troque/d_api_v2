<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use Illuminate\Support\Facades\DB;
use stdClass;

trait RepartoAleatorioParametrizadoTrait
{
    public function repartoAleatorioParametrizado($id_proceso_disciplinario, $id_etapa, $id_fase, $descripcion, $id_rol, $busqueda_usuario, $modulo, $reparto_habilitado, $id_dependencia, $pruebas = 0)
    {
        try {
            $inner_modulo = null;
            $select_modulo = null;
            $where_funcionalidad = null;
            $where_reparto_habilitado = null;

            switch ($modulo) {
                case 'E_GestorRespuesta':
                    $select_modulo = "
                        --VERIFICA QUE EL ROL PERTENESCA A LA FASE Y TENGA PERMISO DE CREAR
                        SELECT
                            r2.id
                        FROM
                        roles r2
                        INNER JOIN users_roles ur2 ON r2.id = ur2.role_id
                        INNER JOIN funcionalidad_rol fr2 ON fr2.role_id = ur2.role_id
                        INNER JOIN mas_funcionalidad mf2 ON fr2.funcionalidad_id = mf2.id
                        INNER JOIN mas_modulo mm2 ON mf2.id_modulo = mm2.id
                        WHERE r2.id = ur.role_id
                        AND mm2.nombre = 'E_GestorRespuesta'
                        AND mf2.nombre = 'Crear'
                        GROUP BY r2.id
                    ";

                    $select_modulo = "AND ur.role_id = (" . $select_modulo . ")";

                    if ($id_rol == null && !$busqueda_usuario) {
                        $inner_modulo = "
                            INNER JOIN mas_funcionalidad mf ON fr.funcionalidad_id = mf.id
                            INNER JOIN mas_modulo mm ON mf.id_modulo = mm.id
                        ";

                        $select_modulo = "
                            AND mm.nombre = 'E_GestorRespuesta'
                            AND mf.nombre = 'Crear'
                        ";
                    }
                    break;
                case 'E_ComunicacionInteresados':
                    /*$select_modulo = "
                        --VERIFICA QUE EL ROL PERTENESCA A LA FASE Y TENGA PERMISO DE CREAR
                        SELECT
                            r2.id
                        FROM
                        roles r2
                        INNER JOIN users_roles ur2 ON r2.id = ur2.role_id
                        INNER JOIN funcionalidad_rol fr2 ON fr2.role_id = ur2.role_id
                        INNER JOIN mas_funcionalidad mf2 ON fr2.funcionalidad_id = mf2.id
                        INNER JOIN mas_modulo mm2 ON mf2.id_modulo = mm2.id
                        WHERE r2.id = ur.role_id
                        AND mm2.nombre = 'E_ComunicacionInteresados'
                        AND mf2.nombre = 'Crear'
                        GROUP BY r2.id
                    ";

                    $select_modulo = "AND ur.role_id = (" . $select_modulo . ")";*/
                    break;
                case 'E_EvaluacionAprobacion':
                    $select_modulo = "AND u.id <> mdo.id_usuario_jefe";
                    break;
                default:
                    $select_modulo = null;
                    break;
            }

            if ($busqueda_usuario) {
                $busqueda_usuario = "AND u.name = '" . $busqueda_usuario . "'";
            }

            if ($id_rol) {
                $where_funcionalidad = "
                    -- CONOCE A QUE ROL VA DIRIGIRSE
                    AND ur.role_id = '$id_rol'
                ";
            }

            if ($busqueda_usuario && !$id_rol) { //REALMENTE SE USA CUANDO SE RECHAZA EL PROCESO, YA QUE BUSCA UN USUARIO QUE TENGA LOS MISMOS PRIVILEGIOS SIN LA VALIDACION
                $select_modulo = null;
            }

            if ($reparto_habilitado) {
                $where_reparto_habilitado = 'AND u.reparto_habilitado = ' . Constants::ESTADOS['activo'];
            }

            if ($busqueda_usuario && !$id_rol) {

                $tipo_expediente = $this->obtenerTipoExpediente($id_proceso_disciplinario);

                $query_consulta_disponibilidad = "
                    SELECT
                        u.name AS nombre_funcionario,
                        u.nombre AS nombre_completo,
                        u.apellido AS apellido_completo,
                        u.email AS email,
                        u.id_dependencia AS id_dependencia,
                        u.reparto_habilitado AS reparto_habilitado,
                        (select count(*) FROM log_proceso_disciplinario where id_funcionario_actual = u.name) AS num_casos_asginados,
                        u.nivelacion AS nivelacion,
                        (u.numero_casos+u.nivelacion) AS num_casos,
                        u.numero_casos
                        --mf.nombre_mostrar AS nombre_mostrar,
                        --mf.id AS id_funcionalidad
                    FROM users_tipo_expediente te
                    LEFT OUTER JOIN users u ON u.ID = te.user_id
                    LEFT OUTER JOIN users_roles ur ON u.id = ur.user_id
                    LEFT OUTER JOIN funcionalidad_rol fr ON ur.role_id = fr.role_id
                    LEFT OUTER JOIN mas_funcionalidad mf ON fr.funcionalidad_id = mf.id
                    WHERE te.tipo_expediente_id = " . $tipo_expediente->id_tipo_expediente . "
                    AND te.sub_tipo_expediente_id = " . $tipo_expediente->sub_tipo_expediente_id . "
                    $where_reparto_habilitado
                    AND u.estado = 1
                    $busqueda_usuario
                    $select_modulo
                    GROUP BY u.NAME, u.NOMBRE, u.APELLIDO, u.id_dependencia, u.email, u.reparto_habilitado, u.nivelacion, u.numero_casos
                ";
            } else if ($busqueda_usuario && $id_rol) {

                $tipo_expediente = $this->obtenerTipoExpediente($id_proceso_disciplinario);

                $query_consulta_disponibilidad = "
                    SELECT
                        u.name AS nombre_funcionario,
                        u.nombre AS nombre_completo,
                        u.apellido AS apellido_completo,
                        u.email AS email,
                        u.id_dependencia AS id_dependencia,
                        u.reparto_habilitado AS reparto_habilitado,
                        (select count(*) FROM log_proceso_disciplinario where id_funcionario_actual = u.name) AS num_casos_asginados,
                        u.nivelacion AS nivelacion,
                        (u.numero_casos+u.nivelacion) AS num_casos
                    FROM users_tipo_expediente te
                    INNER JOIN users u ON u.ID = te.user_id
                    INNER JOIN users_roles ur ON u.id = ur.user_id
                    INNER JOIN funcionalidad_rol fr ON ur.role_id = fr.role_id
                    INNER JOIN mas_funcionalidad mf ON fr.funcionalidad_id = mf.id
                    INNER JOIN mas_modulo mm ON mf.id_modulo = mm.id
                    WHERE te.tipo_expediente_id = " . $tipo_expediente->id_tipo_expediente . "
                    AND te.sub_tipo_expediente_id = " . $tipo_expediente->sub_tipo_expediente_id . "
                    AND u.estado = 1
                    $where_reparto_habilitado
                    $where_funcionalidad
                    $busqueda_usuario
                    $select_modulo
                    GROUP BY u.NAME, u.NOMBRE, u.APELLIDO, u.id_dependencia, u.email, u.reparto_habilitado, u.nivelacion, u.numero_casos
                ";
            } else {

                if($id_dependencia){
                    $dependencia = DB::select("
                        SELECT
                            id_dependencia
                        FROM 
                            USERS
                        WHERE id_dependencia = " . $id_dependencia . "
                    ");
                }
                else{
                    $dependencia = DB::select("
                        SELECT
                            id_dependencia
                        FROM 
                            USERS
                        WHERE NAME = '" . auth()->user()->name . "'
                    ");
                }

                $tipo_expediente = $this->obtenerTipoExpediente($id_proceso_disciplinario);

                $query_consulta_disponibilidad = "
                    SELECT
                        te.user_id AS id_funcionario,
                        u.name AS nombre_funcionario,
                        u.nombre AS nombre_completo,
                        u.apellido AS apellido_completo,
                        u.email AS email,
                        (select count(*) FROM log_proceso_disciplinario where id_funcionario_actual = u.name) AS num_casos_asginados,
                        u.nivelacion AS nivelacion,
                        (u.numero_casos+u.nivelacion) AS num_casos,
                        u.id_dependencia AS id_dependencia,
                        r.name
                    FROM users_tipo_expediente te
                    INNER JOIN users u ON u.ID = te.user_id
                    INNER JOIN users_roles ur ON u.id = ur.user_id
                    INNER JOIN funcionalidad_rol fr ON fr.role_id = ur.role_id
                    INNER JOIN roles r ON ur.role_id = r.id
                    INNER JOIN mas_dependencia_origen mdo ON u.id_dependencia = mdo.id
                    $inner_modulo
                    WHERE te.tipo_expediente_id = " . $tipo_expediente->id_tipo_expediente . "
                    AND te.sub_tipo_expediente_id = " . $tipo_expediente->sub_tipo_expediente_id . "
                    AND u.estado = 1
                    $where_reparto_habilitado
                    AND u.id_dependencia = " . $dependencia[0]->id_dependencia . "
                    $where_funcionalidad
                    $select_modulo
                    GROUP BY te.user_id, u.name, u.nombre, u.apellido, u.id_dependencia, r.name, u.email, u.nivelacion, u.numero_casos
                    ORDER BY u.numero_casos ASC
                ";
            }

            $respuesta_usuario_actual = DB::select($query_consulta_disponibilidad);

            if ($pruebas == 0) {
                // dd(
                //     "modulo: ". $modulo,
                //     "id_proceso_disciplinario: " . $id_proceso_disciplinario,
                //     "id_etapa: " . $id_etapa,
                //     "id_fase: " . $id_fase,
                //     "descripcion: " . $descripcion,
                //     "id_rol: " . $id_rol,
                //     "busqueda_usuario: " . $busqueda_usuario,
                //     "pruebas: ". $pruebas,
                //     $query_consulta_disponibilidad,
                //     $respuesta_usuario_actual
                // );
                // error_log("modulo: ". $modulo);
                // error_log("id_proceso_disciplinario: " . $id_proceso_disciplinario);
                // error_log("id_etapa: " . $id_etapa);
                // error_log("id_fase: " . $id_fase);
                // error_log("descripcion: " . $descripcion);
                // error_log("id_rol: " . $id_rol);
                // error_log("busqueda_usuario: " . $busqueda_usuario);
                // error_log("pruebas: ". $pruebas);
            }

            if ($busqueda_usuario && count($respuesta_usuario_actual) <= 0) { //SI NO SE BUSCA USUARIO, SE BUSCARA UN REMPLAZO
                $resultado = $this->repartoAleatorioParametrizado($id_proceso_disciplinario, $id_etapa, $id_fase, null, $id_rol, null, $modulo, true, $id_dependencia, $pruebas + 1);
                return $resultado;
            } else if (count($respuesta_usuario_actual) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = 'No existen funcionarios con permisos para gestionar este tipo de expediente.';
                return $error;
            }

            $results = new stdClass;
            $results->id_dependencia_origen = $respuesta_usuario_actual[0]->id_dependencia;
            $results->id_funcionario_actual = $respuesta_usuario_actual[0]->nombre_funcionario;
            $results->id_funcionario_asignado = $respuesta_usuario_actual[0]->nombre_funcionario;
            $results->nombre_funcionario = $respuesta_usuario_actual[0]->nombre_funcionario;
            $results->nombre_completo = $respuesta_usuario_actual[0]->nombre_completo;
            $results->apellido_completo = $respuesta_usuario_actual[0]->apellido_completo;
            $results->email = $respuesta_usuario_actual[0]->email;
            $results->num_casos = $respuesta_usuario_actual[0]->num_casos;
            $results->estado = true;
            return $results;
        } catch (\Exception $e) {
            //dd($e);
            error_log($e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = 'No existen funcionarios con permisos para gestionar este tipo de expediente.';
            return $error;
        }
    }

    private function obtenerTipoExpediente($id_proceso_disciplinario)
    {
        try {

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
                WHERE id_proceso_disciplinario = '$id_proceso_disciplinario'
                ORDER BY created_at DESC
            ");

            // se valida el tipo de expediente y se asigna el sub tipo de expediente
            if ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
                $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_derecho_peticion;
            } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
                $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
            } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja'] || $tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {
                $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
            } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']) {
                if ($tipo_expediente[0]->fecha_termino != null) {
                    $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['dias'];
                } else {
                    $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['horas'];
                }
            }

            return $tipo_expediente[0];
        } catch (\Exception $e) {
            error_log($e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = 'No existen funcionarios con permisos para gestionar este tipo de expediente.';
            return $error;
        }
    }
}
