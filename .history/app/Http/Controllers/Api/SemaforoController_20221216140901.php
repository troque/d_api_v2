<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SemaforoFormRequest;
use App\Http\Resources\Semaforo\SemaforoCollection;
use App\Http\Resources\Semaforo\SemaforoResource;
use App\Models\SemaforoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

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
        return SemaforoCollection::make($this->repository->paginate($request->limit ?? 100));
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

    public function semaforoPorMasActuacion($id_mas_actuacion)
    {
        $query = $this->repository->customQuery(function ($model) use ($id_mas_actuacion) {
            return $model->where('id_mas_actuacion_inicia', $id_mas_actuacion)
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
                ->get();
        });
        return SemaforoCollection::make($query);
    }
}
