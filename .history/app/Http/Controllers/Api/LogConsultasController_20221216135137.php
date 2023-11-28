<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogConsultasFormRequest;
use App\Http\Resources\LogConsultas\LogConsultasCollection;
use App\Http\Resources\LogConsultas\LogConsultasResource;
use App\Models\LogConsultasModel;
use App\Repositories\RepositoryGeneric;
use Error;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class LogConsultasController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new LogConsultasModel());
    }

    public function index(Request $request)
    {
        return LogConsultasCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    public function store(LogConsultasFormRequest $request)
    {

        try {

            $datosRequest = $request->validated()["data"]["attributes"];
            return LogConsultasResource::make($this->repository->create($datosRequest));
        } catch (QueryException  $e) {

            $error['estado'] = false;
            $error['error'] = $e->getMessage();
            return json_encode($error);
        }
    }

    public function show($id)
    {
        return LogConsultasResource::make($this->repository->find($id));
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
