<?php

namespace App\Http\Utilidades;

use DateTime;

class Utilidades
{

    /**
     * @param $linea
     * @return string
     */
    public static function getSubstring($linea){

        if (strpos($linea, 'ORA-00001') !== false) {
            return "ORA-00001: Ya existe un archivo con ese nombre.";
        }

        return $linea;
    }


    /**
     * Retorna el número de dias calendario entre dos fechas
     */
    public static function getNumeroDiasEntreFechas($fecha_inicial){

        $date1 = new DateTime($fecha_inicial);
        $date2 = new DateTime(date('d/m/Y'));
        $diff = $date1->diff($date2);

        return $diff->days;
    }

    /**
     * Retorna el número de dias habiles entre dos fechas
     */
    public static function getNumeroDiasCalendario($fecha_inicial, $contador_no_laborales){

        $date1 = new DateTime($fecha_inicial);
        $date2 = new DateTime(date('d/m/Y'));
        $diff = $date1->diff($date2);

        return ($diff->days) - $contador_no_laborales;
    }


    /**
     *
     */
    public static function getDescripcionCorta($texto){

        if(strlen ($texto)>=40){
            return substr($texto, 0, 40);
        }

        return $texto;

    }




}
