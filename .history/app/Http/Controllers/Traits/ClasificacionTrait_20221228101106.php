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
            where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "'");

        if (count($proceso_disciplinario) > 0 && $proceso_disciplinario[0]->id_tipo_proceso == Constants::TIPO_DE_PROCESO['desglose']) {

            $validar_clasificacion = DB::select("select id_clasificacion_radicado
                where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "' and estado = 1 and eliminado = 0");

            $tipo_expediente = DB::select("select id_tipo_expediente, id_tipo_queja
                where id_proceso_disciplinario = '" . $id_proceso_disciplinario . "' and uuid = '" . $validar_clasificacion[0]->id_clasificacion_radicado . "'");

            if ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja']) {
                $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
            }
        }



        // se valida el tipo de expediente y se asigna el sub tipo de expediente
        if ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
            $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_derecho_peticion;
        } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
            $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
        } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja']) {
            $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
        } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']) {
            if ($tipo_expediente[0]->fecha_termino != null) {
                $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['dias'];
            } else {
                $tipo_expediente[0]->sub_tipo_expediente_id = Constants::TIPO_TUTELA['horas'];
            }
        } elseif ($tipo_expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {

            // CONSULTAR PROCESO PADRE SI APLICA
            $validar_clasificacion = DB::select("
            select id_clasificacion_radicado where id_proceso_disciplinario = '" . $datosRecibidos['id_proceso_disciplinario'] . "' and estado = 1 and eliminado = 0");


            $tipo_expediente[0]->sub_tipo_expediente_id = $tipo_expediente[0]->id_tipo_queja;
        }



        $repository_antecedente = new RepositoryGeneric();
        $repository_antecedente->setModel(new AntecedenteModel());
        $antecedenteRequest['descripcion'] = substr($antecedenteRequest['descripcion'], 0, 4000);
        $antecedente = $repository_antecedente->create($antecedenteRequest);

        return $antecedente->uuid;
    }
}
