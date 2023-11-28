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
        $procesos_activos = DB::select("SELECT count(*)
            FROM log_proceso_disciplinario lpd
            WHERE id_funcionario_actual = '".auth()->user()->name;."'");
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
