<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SemaforoFormRequest;
use App\Http\Resources\Semaforo\SemaforoCollection;
use App\Http\Resources\Semaforo\SemaforoResource;
use App\Models\SemaforoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemaforoController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new SemaforoModel());
    }

    public function index(Request $request)
    {
        //return SemaforoCollection::make($this->repository->paginate($request->limit ?? 100));
        return SemaforoCollection::make(SemaforoModel::orderByDesc('created_at')->get());
    }

    public function store(SemaforoFormRequest $request)
    {
        try {
            return SemaforoResource::make($this->repository->create($request->validated()["data"]["attributes"]));
        } catch (\Exception $e) {

            if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                $error['estado'] = false;
                $error['error'] = 'Ya existe un registro con ese nombre.';

                return json_encode($error);
            }
        }
    }

    public function show($id)
    {
        return SemaforoResource::make($this->repository->find($id));
    }

    public function update(SemaforoFormRequest $request, $id)
    {
        return SemaforoResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    public function semaforoPorMasActuacion($id_mas_actuacion, $id_etapa)
    {
        $query = $this->repository->customQuery(function ($model) use ($id_mas_actuacion, $id_etapa) {
            return $model->where('id_mas_actuacion_inicia', $id_mas_actuacion)
                ->where('id_etapa', $id_etapa)
                ->where('estado', '1')
                ->get();
        });
        return SemaforoCollection::make($query);
    }

    public function semaforoPorEventoInicio($idEventoInicio)
    {
        $query = $this->repository->customQuery(function ($model) use ($idEventoInicio) {
            return $model->where('id_mas_evento_inicio', $idEventoInicio)
                ->where('estado', '1')
                ->orderBy('created_at')
                ->get();
        });
        return SemaforoCollection::make($query);
    }

    public function semaforoPorEtapa($id_etapa, $estado)
    {
        //return SemaforoCollection::make($this->repository->paginate($request->limit ?? 100));
        return SemaforoCollection::make(SemaforoModel::where('estado', $estado)->where('id_etapa', $id_etapa)->orderByDesc('created_at')->get());
    }

    public function obtenerSemaforosProcesoDisciplinario($id_evento, $id_proceso_disciplinario){

        $query = DB::select("
            SELECT
                pdps.id,
                s.id,
                s.nombre,
                s.id_mas_dependencia_inicia,
                s.id_mas_grupo_trabajo_inicia,
                s.id_mas_actuacion_inicia
            FROM
            semaforo s
            INNER JOIN proceso_disciplinario_por_semaforo pdps ON s.id = pdps.id_semaforo
            WHERE s.id_mas_evento_inicio = ".$id_evento."
            AND pdps.id_proceso_disciplinario = '".$id_proceso_disciplinario."'
            AND pdps.estado = 1
            AND pdps.eliminado IS null
            AND pdps.finalizo IS null
        ");

        return json_encode($query);
    }
}
