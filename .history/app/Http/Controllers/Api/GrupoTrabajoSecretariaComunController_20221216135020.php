<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GrupoTrabajoSecretariaComunFormRequest;
use App\Http\Resources\GrupoTrabajoSecretariaComun\GrupoTrabajoSecretariaComunCollection;
use App\Http\Resources\GrupoTrabajoSecretariaComun\GrupoTrabajoSecretariaComunResource;
use App\Models\GrupoTrabajoSecretariaComunModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Traits\RepartoAleatorioTrait;
use App\Http\Utilidades\Constants;

class GrupoTrabajoSecretariaComunController extends Controller
{

    private $repository;
    use RepartoAleatorioTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new GrupoTrabajoSecretariaComunModel());
    }

    public function index(Request $request)
    {
        return GrupoTrabajoSecretariaComunCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    public function store(GrupoTrabajoSecretariaComunFormRequest $request)
    {
        DB::connection()->beginTransaction();
        $datosRequest = $request->validated()["data"]["attributes"];
        $respuesta = GrupoTrabajoSecretariaComunResource::make($this->repository->create($datosRequest));
        DB::connection()->commit();
        return $respuesta;
    }

    public function show($id)
    {
        return GrupoTrabajoSecretariaComunResource::make($this->repository->find($id));
    }

    public function update(GrupoTrabajoSecretariaComunFormRequest $request, $id)
    {
        return GrupoTrabajoSecretariaComunResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    public function repartoAleatorio($id_grupoTrabajo)
    {
        $funcionario_asignado = GrupoTrabajoSecretariaComunController::storeRepartoAleatorioPorGrupo(auth()->user()->name, Constants::DEPENDENCIA['secretaria_comun'], $id_grupoTrabajo);
        return $funcionario_asignado;
    }
}
