<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\AsignacionProcesoDisciplinario\AsignacionProcesoDisciplinarioResource;
use App\Models\AsignacionProcesoDisciplinarioModel;


trait AsignacionProcesoDisciplinarioTrait
{
    public static function storeAsignacionProcesoDisciplinario($request)
    {
        $asignacionModel = new AsignacionProcesoDisciplinarioModel();
        return AsignacionProcesoDisciplinarioResource::make($asignacionModel->create($request));
    }
}

