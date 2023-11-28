<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\DB;

trait UtilidadesTrait
{
    public static function getNombreFase($id_fase)
    {

        $fase = DB::select("select nombre from mas_fase where id = " . $id_fase);

        return $fase[0]->nombre;
    }

    /**
     *
     */
    public static function getEstadoFaseProcesoDisciplinario($proceso_disciplinario, $id_fase)
    {

        $log = DB::select("select mas_fase.id as id_fase, mas_fase.nombre,
        (select count(log_proceso_disciplinario.id_fase) from log_proceso_disciplinario
        where log_proceso_disciplinario.eliminado = 0 and log_proceso_disciplinario.id_fase = mas_fase.id and log_proceso_disciplinario.id_proceso_disciplinario = '" . $proceso_disciplinario . "') as num_registros
        from mas_fase where mas_fase.id = " . $id_fase);


        $aux = "select mas_fase.id as id_fase, mas_fase.nombre,
        (select count(log_proceso_disciplinario.id_fase) from log_proceso_disciplinario
        where log_proceso_disciplinario.eliminado = 0 and log_proceso_disciplinario.id_fase = mas_fase.id and log_proceso_disciplinario.id_proceso_disciplinario = '" . $proceso_disciplinario . "') as num_registros
        from mas_fase where mas_fase.id = " . $id_fase;

        error_log($aux);

        if (count($log) == 0) {
            return 0;
        }

        if ($log[0]->num_registros > 0) {
            return 1;
        }

        return 0;
    }


    public static function eliminar_tildes($cadena)
    {

        //Codificamos la cadena en formato utf8 en caso de que nos de errores
        //$cadena = utf8_encode($cadena);

        //Ahora reemplazamos las letras
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );

        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena
        );

        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena
        );

        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena
        );

        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena
        );

        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $cadena
        );

        return $cadena;
    }
}
