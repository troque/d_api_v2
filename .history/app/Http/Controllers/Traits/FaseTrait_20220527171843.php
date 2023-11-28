<?php

namespace App\Http\Controllers\Traits;


use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use Illuminate\Support\Facades\DB;

trait FaseTrait
{

    public static function getNombreFase($id_fase){

        DB::table('log_proceso_disciplinario')
        ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
        ->update(['id_funcionario_actual' => null, 'id_estado' => Constants::ESTADO_PROCESO_DISCIPLINARIO['contestado']]);

    }





}

