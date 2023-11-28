<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\Antecedente\AntecedenteResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Models\AntecedenteModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;

trait EatpaTrait
{

    /**
     *
     */
    public static function storeAntecedente($antecedenteRequest)
    {
        $repository_antecedente = new RepositoryGeneric();
        $repository_antecedente->setModel(new AntecedenteModel());
        $antecedenteRequest['descripcion'] = substr($antecedenteRequest['descripcion'], 0, 4000);
        return AntecedenteResource::make($repository_antecedente->create($antecedenteRequest));
    }
}

