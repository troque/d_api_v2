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

        $query = DB::select("select
        u.name,
        mm.nombre,
        mf.nombre
        from users_roles ur
        inner join roles r on ur.role_id = r.id
        inner join funcionalidad_rol fr on fr.role_id = r.id
        inner join mas_funcionalidad mf on mf.id = fr.funcionalidad_id
        inner join mas_modulo mm on mm.id = mf.id_modulo
        inner join users u on u.id = ur.user_id
        where u.name = '" . $user_name . "' and (mm.nombre='B_ConsultarProcesoCompletoAsignado' or  mm.nombre='B_ConsultarProcesoCompletoGeneral') and mf.nombre='Consultar'");


        if ($query != null) {
            return true;
        }
        return false;

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
