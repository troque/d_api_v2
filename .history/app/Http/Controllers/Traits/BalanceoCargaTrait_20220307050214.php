<?php

namespace App\Http\Controllers\Traits;


use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Models\LogProcesoDisciplinarioModel;

trait LogTrait
{
    public static function storeLogProcesoDisciplinario($request)
    {
        $logModel = new LogProcesoDisciplinarioModel();
        return LogProcesoDisciplinarioResource::make($logModel->create($request));
    }
}

