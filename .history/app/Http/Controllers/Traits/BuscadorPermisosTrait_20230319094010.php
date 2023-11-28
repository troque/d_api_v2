<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\ClasificacionRadicado\ClasificacionRadicadoResource;
use App\Http\Utilidades\Constants;
use App\Models\ClasificacionRadicadoModel;
use Illuminate\Support\Facades\DB;

trait BuscadorPermisosTrait
{

    public static function validarPermisosBuscadorTrait($user_name)
    {

        // se valida el tipo de expediente

        $buscador = DB::select("select id_tipo_expediente, id_tipo_queja, id_tipo_derecho_peticion,
        fecha_termino, hora_termino from clasificacion_radicado
        where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "' and estado = 1");



        $aux = "select te.user_id as id_funcionario,
        from users_tipo_expediente te
        inner join users u on u.id = te.user_id
        where te.tipo_expediente_id = " . $tipo_expediente[0]->id_tipo_expediente . "
        and te.sub_tipo_expediente_id = " . $tipo_expediente[0]->sub_tipo_expediente_id . "
        and u.name = '" . $user_name . "' and u.reparto_habilitado = 1 and u.estado = 1";

        // error_log($aux);

        $results = DB::select("select te.user_id as id_funcionario
        from users_tipo_expediente te
        inner join users u on u.id = te.user_id
        where te.tipo_expediente_id = " . $tipo_expediente[0]->id_tipo_expediente . "
        and te.sub_tipo_expediente_id = " . $tipo_expediente[0]->sub_tipo_expediente_id . "
        and u.name = '" . $user_name . "' and u.reparto_habilitado = 1 and u.estado = 1");

        if ($results != null) {
            return true;
        }

        return false;
    }
}
