<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActuacionPorSemaforoFormRequest;
use App\Http\Resources\ActuacionPorSemaforo\ActuacionPorSemaforoCollection;
use App\Http\Resources\ActuacionPorSemaforo\ActuacionPorSemaforoResource;
use App\Models\ActuacionPorSemaforoModel;
use App\Models\DiasNoLaboralesModel;
use App\Repositories\RepositoryGeneric;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActuacionPorSemaforoController extends Controller
{

    /**
     *
     */
    public function index()
    {
        $procesos_activos = DB::select("SELECT count(*) AS cant_procesos_activos
            FROM log_proceso_disciplinario lpd
            WHERE lpd.id_funcionario_actual = '" . auth()->user()->name . "'");

        $documentos_pendientes_firma = DB::select("SELECT count(*) as cant_firma_pendiente
            FROM firma_actuaciones mtf
            WHERE mtf.id_user = " . auth()->user()->id . " and mtf.estado = 1");

        $reciboDatos['attributes']['cat_procesos_activos'] = $procesos_activos[0]->cant_procesos_activos;
        $reciboDatos['attributes']['cat_documentos_sin_firmar'] = $documentos_pendientes_firma[0]->cant_firma_pendiente;
    }

    /**
     *
     */
    public function store()
    {
    }

    public function show()
    {
    }

    /**
     *
     */
    public function update()
    {
    }
}
