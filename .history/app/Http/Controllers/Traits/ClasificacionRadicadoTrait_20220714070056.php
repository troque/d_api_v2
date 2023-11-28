<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\Antecedente\AntecedenteResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Models\AntecedenteModel;
use App\Models\ClasificacionRadicadoModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;

trait ClasificacionRadicadoTrait
{

    /**
     *
     */
    public static function storeClasificacionRadicado($id_proceso_disciplinario)
    {
        $repository = new RepositoryGeneric();
        $repository->setModel(new ClasificacionRadicadoModel());

        $request['id_proceso_disciplinario'] = $id_proceso_disciplinario;
        $request['id_etapa'] = LogTrait::etapaActual($id_proceso_disciplinario);
        $request['id_tipo_expediente'] = $id_proceso_disciplinario;
        $request['observaciones'] = $id_proceso_disciplinario;
        $request['id_tipo_queja'] = $id_proceso_disciplinario;
        $request['id_termino_respuesta'] = $id_proceso_disciplinario;
        $request['estado'] = $id_proceso_disciplinario;
        $request['id_dependencia'] = $id_proceso_disciplinario;


        $clasificacion = $repository->create($request);
        return $clasificacion->uuid;



        //return AntecedenteResource::make($repository_antecedente->create($antecedenteRequest));
    }
}

