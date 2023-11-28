<?php

namespace App\Http\Controllers\Traits;


use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use Illuminate\Support\Facades\DB;

trait FaseTrait
{

    public static function getNombreFase($id_fase){

        $fase = DB::table('mas_fases')->select('nombre')->where('id', $id_fase);

        return $fase->nombre;

    }





}

