<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CondicionFormRequest;
use App\Http\Resources\Condicion\CondicionCollection;
use App\Http\Resources\Condicion\CondicionResource;
use App\Models\CondicionModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class CondicionController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new CondicionModel());
    }

    public function index(Request $request)
    {
        return CondicionCollection::make($this->repository->paginate($request->limit ?? 100));
    }

    public function store(CondicionFormRequest $request)
    {
        try {
            return CondicionFormRequest::make($this->repository->create($request->validated()["data"]["attributes"]));
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
        return CondicionResource::make($this->repository->find($id));
    }

    public function update(CondicionFormRequest $request, $id)
    {
        return CondicionResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    public function condicionesPorSemaforo($id_semaforo)
    {
        $query = $this->repository->customQuery(function ($model) use ($id_semaforo) {
            return $model->where('id_semaforo', $id_semaforo)
                ->where('estado', 1)
                ->orderBy('inicial', 'asc')
                ->get();
        });
        return CondicionCollection::make($query);
    }
}
