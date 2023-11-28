<?php

namespace App\Http\Controllers\Traits;

use App\Models\User;
use Illuminate\Support\Facades\DB;

trait NumeroCasosTrait
{
    public static function numeroCasosUsuario($usuario, $casosASumar = 1, $adicional = false)
    {

        $user = DB::select("
            SELECT
                (select count(*) FROM log_proceso_disciplinario where id_funcionario_actual = u.name) AS num_casos_asginados,
                u.nivelacion
            FROM
                users u
            WHERE u.name ='" . $usuario . "'
        ");

        $user[0]->numero_casos = $user[0]->nivelacion + $user[0]->num_casos_asginados;

        if ($user[0]->nivelacion > 0) {
            $user[0]->nivelacion += -0.5;
        } else if ($user[0]->nivelacion < 0) {
            $user[0]->nivelacion = 0;
        }

        if ($adicional) {
            $user[0]->numero_casos += 1;
            // error_log("Se agrega adicional");
        }

        $resultado = User::where('name', $usuario)->update(['numero_casos' => $user[0]->numero_casos, 'nivelacion' => $user[0]->nivelacion]);
        // error_log("Resultado -> " . User::where('name', $usuario)->get());
        //User::where('name', $usuario)->update(['numero_casos' => ($user[0]->num_casos_asginados + $user[0]->nivelacion + $casosASumar), 'nivelacion' => $user[0]->nivelacion]);
    }
}
