<?php

namespace App\Http\Controllers\Traits;


use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Models\AsignacionProcesoDisciplinarioModel;
use App\Models\LogProcesoDisciplinarioModel;

trait LogTrait
{
    public static function storeAsignacionProcesoDisciplinario($request)
    {
        $asignacionModel = new AsignacionProcesoDisciplinarioModel();
        return AsignacionProcesoDisciplinarioModelResource::make($asignacionModel->create($request));
    }
}

