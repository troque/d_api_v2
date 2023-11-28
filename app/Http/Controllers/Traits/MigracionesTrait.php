<?php

namespace App\Http\Controllers\Traits;

use App\Services\MigracionWS;
use Error;
use stdClass;

trait MigracionesTrait
{
    /**
     * Consulta todos los radicados que a partir de ciertos parmaetros de búsqueda.
     * fechaRegistroDesde, fechaRegistroHasta, version, vigencia, numeroRadicado, nombreResponsable, nombreResponsable,
     * idResponsable, dependencia, idDependencia, tipoInteresado
     *
     */
    public static function buscarExpedientePorNumeroRadicado($data)
    {

        $migracion = new MigracionWS();
        $migracion->login();
        $respuesta_consulta = $migracion->consultarRadicado($data);

        if (!isset($respuesta_consulta)) {
            if (!$respuesta_consulta['objectresponse']) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "No se encuentran resultados del numero de radicado digitado";

                return $error;
            }
        } else {
            return $respuesta_consulta;
        }

        return $respuesta_consulta;
    }


    public static function buscarDetalleExpediente($expediente, $vigencia)
    {
        $migracion = new MigracionWS();
        $migracion->login();

        return $migracion->consultarExpediente($expediente, $vigencia);
    }
}
