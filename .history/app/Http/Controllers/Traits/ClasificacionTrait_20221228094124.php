<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\Antecedente\AntecedenteResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Models\AntecedenteModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;

trait ClasificacionTrait
{

    /**
     *
     */
    public static function validarClasificacion($id_proceso_disciplinario)
    {
        $repository_antecedente = new RepositoryGeneric();
        $repository_antecedente->setModel(new AntecedenteModel());
        $antecedenteRequest['descripcion'] = substr($antecedenteRequest['descripcion'], 0, 4000);
        $antecedente = $repository_antecedente->create($antecedenteRequest);

        return $antecedente->uuid;
    }
}
