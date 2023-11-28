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

    public function repartoAleatorio($id_grupoTrabajo, $id_proceso_disciplinario)
    {

        $dependenciaSecretaria = DB::select(
            "
                SELECT 
                    MP.valor
                FROM MAS_PARAMETRO MP
                WHERE MP.nombre IN ('id_dependencia_secretaria_comun')
                AND MP.estado = " . Constants::ESTADOS['activo'] . "
            "
        );

        if(count($dependenciaSecretaria) <= 0){
            $error['estado'] = false;
            $error['error'] = 'ERROR AL MOMENTO DE OBTENER INFORMACIÓN DEL PROCESO.';
            return json_encode($error);
        }

        $proceso_disciplinario = DB::select(
            "
                SELECT
                    cr.id_tipo_expediente,
                    cr.id_tipo_queja,
                    cr.created_at
                FROM
                proceso_disciplinario pd
                INNER JOIN clasificacion_radicado cr ON pd.uuid = cr.id_proceso_disciplinario
                WHERE pd.uuid = '$id_proceso_disciplinario'
                ORDER BY cr.created_at DESC
            "
        );

        if(count($proceso_disciplinario) <= 0){
            $error['estado'] = false;
            $error['error'] = 'ERROR AL MOMENTO DE OBTENER INFORMACIÓN DEL PROCESO.';
            return json_encode($error);
        }

        $funcionario_asignado = GrupoTrabajoSecretariaComunController::storeRepartoAleatorioPorGrupo(auth()->user()->name, $dependenciaSecretaria[0]->valor, $id_grupoTrabajo, $proceso_disciplinario[0]->id_tipo_queja);
        return $funcionario_asignado;
    }
}
