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
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ActuacionPorSemaforoModel());
    }

    /**
     *
     */
    public function index(Request $request)
    {
    }

    /**
     *
     */
    public function store(ActuacionPorSemaforoFormRequest $request)
    {
    }

    public function show($id)
    {
    }

    /**
     *
     */
    public function update(ActuacionPorSemaforoFormRequest $request, $id)
    {
    }

    public function existeSemaforoConFecha($id_semaforo)
    {
    }

    /**
     *
     */
    public function getSemaforosPorProceso($uuid)
    {
    }

    public function diasNoLaborales($inicio, $fin)
    {
    }

    public function getDiasTranscurridos($uuid)
    {
    }

    public function FinalizarSemaforo($id_semaforo, $id_proceso_disciplinario)
    {
    }
}
