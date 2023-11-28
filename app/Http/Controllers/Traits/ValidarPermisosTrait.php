<?php

namespace App\Http\Controllers\Traits;

use App\Http\Utilidades\Constants;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use stdClass;

trait ValidarPermisosTrait
{

    public static function validarPermisoTrait($user_name, $modulo, $funcionalidad)
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
        where u.name = '" . $user_name . "' and mm.nombre='" . $modulo . "' and mf.nombre='" . $funcionalidad . "'");


        if ($query != null) {
            return true;
        }
        return false;
    }


    public static function validarUsuarioAsignado($user_name, $id_proceso_disciplinario)
    {

        // se valida el tipo de expediente

        $query = DB::select("select
            id_funcionario_actual
            from
            log_proceso_disciplinario
            where id_funcionario_actual = '" . $user_name . "' and id_proceso_disciplinario = '" . $id_proceso_disciplinario . "'");


        if ($query != null) {
            return true;
        }
        return false;
    }

    public static function validarUsuarioPermisos($user_name)
    {
        $user = User::where('name', $user_name)->where('estado', Constants::ESTADOS['activo'])->where('reparto_habilitado', Constants::ESTADOS['activo'])->get();
        if(count($user) > 0){
            return true;
        }
        return false;
    }

}
