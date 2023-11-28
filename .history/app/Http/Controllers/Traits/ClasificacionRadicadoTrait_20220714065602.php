<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\Antecedente\AntecedenteResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Models\AntecedenteModel;
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
        $antecedenteRequest['descripcion'] = substr($antecedenteRequest['descripcion'], 0, 4000);
        $antecedente = $repository_antecedente->create($antecedenteRequest);
        return $antecedente->uuid;
        //return AntecedenteResource::make($repository_antecedente->create($antecedenteRequest));
    }
}

