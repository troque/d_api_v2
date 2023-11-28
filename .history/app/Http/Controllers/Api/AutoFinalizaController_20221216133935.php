<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutoFinalizaFormRequest;
use App\Http\Resources\AutoFinalizas\AutoFinalizaCollection;
use App\Http\Resources\AutoFinalizas\AutoFinalizaResource;
use App\Models\AutoFinalizaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;


class AutoFinalizaController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new AutoFinalizaModel());
    }

    public function index(Request $request)
    {
        return AutoFinalizaCollection::make($this->repository->paginate($request->limit ?? 100));
    }

    public function store(AutoFinalizaFormRequest $request)
    {
        try {
            return AutoFinalizaFormRequest::make($this->repository->create($request->validated()["data"]["attributes"]));
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
        return AutoFinalizaResource::make($this->repository->find($id));
    }

    public function update(AutoFinalizaFormRequest $request, $id)
    {
        return AutoFinalizaResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    public function AutoFinalizanPorSemaforo($id_semaforo)
    {
        $query = $this->repository->customQuery(function ($model) use ($id_semaforo) {
            return $model->where('id_semaforo', $id_semaforo)
                ->get();
        });
        return AutoFinalizaCollection::make($query);
    }

    public function AutoFinalizanPorMasActuacion($id_mas_actuacion)
    {
        $query = $this->repository->customQuery(function ($model) use ($id_mas_actuacion) {
            return $model->where('id_mas_actuacion', $id_mas_actuacion)
                ->where('estado', '1')
                ->get();
        });
        return AutoFinalizaCollection::make($query);
    }
}
