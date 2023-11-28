<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasEventoInicioFormRequest;
use App\Http\Resources\MasEventoInicio\MasEventoInicioCollection;
use App\Http\Resources\MasEventoInicio\MasEventoInicioResource;
use App\Models\MasEventoInicioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class MasEventoInicioController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository) {
        $this->repository = $repository;
        $this->repository->setModel(new MasEventoInicioModel());
    }

    public function index(Request $request)
    {   
        return MasEventoInicioCollection::make($this->repository->paginate($request->limit ?? 100));
    }

    public function store(MasEventoInicioFormRequest $request)
    {
         try{
            return MasEventoInicioFormRequest::make($this->repository->create($request->validated()));
        } catch (\Exception $e) {

            if(strpos($e->getMessage(), 'ORA-00001') !== false)  {

                $error['estado'] = false;
                $error['error'] = 'Ya existe un registro con ese nombre.';

                return json_encode($error);
            }

        }
    }

    public function show($id)
    {
        return MasEventoInicioResource::make($this->repository->find($id));
    }

    public function update(MasEventoInicioFormRequest $request, $id)
    {
        return MasEventoInicioResource::make($this->repository->update($request->validated(),$id));
    }

}
