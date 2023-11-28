<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\Antecedente\AntecedenteResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\AntecedenteModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Support\Facades\DB;

trait ClasificacionTrait
{

    /**
     *
     */
    public static function validarClasificacionDesglose($id_proceso_disciplinario)
    {

        $proceso_disciplinario = DB::select("select id_tipo_proceso, radicado_padre from proceso_disciplinario
            where uuid = '" . $id_proceso_disciplinario . "'");

        if (count($proceso_disciplinario) > 0 && $proceso_disciplinario[0]->id_tipo_proceso == Constants::TIPO_DE_PROCESO['desglose']) {

            $validar_clasificacion = DB::select("select id_clasificacion_radicado from validar_clasificacion
                where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "' and estado = 1 and eliminado = 0");

            $tipo_expediente = DB::select("select id_tipo_expediente, id_tipo_queja
                where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "' and uuid = '" . $validar_clasificacion[0]->id_clasificacion_radicado . "'");

            return $tipo_expediente;
        }

        return null;
    }
}
