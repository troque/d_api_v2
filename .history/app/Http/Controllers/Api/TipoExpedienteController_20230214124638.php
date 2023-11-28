<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoExpedienteFormRequest;
use App\Http\Resources\TipoDerechoPeticion\TipoDerechoPeticionCollection;
use App\Http\Resources\TipoExpediente\TipoExpedienteCollection;
use App\Http\Resources\TipoExpediente\TipoExpedienteResource;
use App\Http\Resources\TipoQueja\TipoQuejaCollection;
use App\Http\Utilidades\Constants;
use App\Models\ClasificacionRadicadoModel;
use App\Models\TipoDerechoPeticionModel;
use App\Models\TipoExpedienteModel;
use App\Models\TipoQuejaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoExpedienteController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoExpedienteModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TipoExpedienteCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function geTipoExpedienteSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return TipoExpedienteCollection::make($query);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TipoExpedienteFormRequest $request)
    {
        return TipoExpedienteResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TipoExpedienteResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoExpedienteFormRequest $request, $id)
    {
        return TipoExpedienteResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getExpedientesByTipoProcesoDisciplinario($id_proceso_disciplinario)
    {

        $tipo_proceso_disciplinario = DB::select("select id_tipo_proceso from proceso_disciplinario where uuid = '" . $id_proceso_disciplinario . "'");

        if ($tipo_proceso_disciplinario != null && $tipo_proceso_disciplinario[0]->id_tipo_proceso == Constants::TIPO_DE_PROCESO['poder_preferente']) {

            $query = $this->repository->customQuery(
                function ($model) {
                    return $model->where("id", Constants::TIPO_EXPEDIENTE['poder_referente'])->orderBy("nombre", "asc")->get();
                }
            );
            return TipoExpedienteCollection::make($query);
        } else {

            $query = $this->repository->customQuery(
                function ($model) {
                    return $model->where('estado', true)->orderBy("nombre", "asc")->get();
                }
            );

            return TipoExpedienteCollection::make($query);
        }
    }







    public function getTiposExpedientesHabilitados($id_proceso_disciplinario)
    {

        $query = TipoExpedienteModel::query();

        $expediente = ClasificacionRadicadoModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)
            ->where('estado', Constants::ESTADOS['activo'])
            ->where('id_tipo_expediente', Constants::TIPO_EXPEDIENTE['poder_referente'])
            ->get();

        if (count($expediente) > 0) {

            $query = $query->select('mas_tipo_expediente.id', 'mas_tipo_expediente.nombre', 'mas_tipo_expediente.termino', 'mas_tipo_expediente.estado')->where('id', '<>', $expediente[0]->id_tipo_expediente)->orderBy('mas_tipo_expediente.nombre', 'asc')->get();

            return TipoExpedienteCollection::make($query);
        }

        $query = $query->select('mas_tipo_expediente.id', 'mas_tipo_expediente.nombre', 'mas_tipo_expediente.termino', 'mas_tipo_expediente.estado')->orderBy('mas_tipo_expediente.nombre', 'asc')->get();

        return TipoExpedienteCollection::make($query);
    }

    /**
     *
     */
    public function getNombreTipoExpediente($id_tipo_expediente)
    {
        $query = TipoExpedienteModel::query();
        $query = $query->where("id", $id_tipo_expediente)->get();

        return TipoExpedienteCollection::make($query);
    }


    public function getNombreSubTipoExpediente($id_tipo_expediente, $id_sub_tipo_expediente)
    {

        if ($id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
            $query = TipoDerechoPeticionModel::query();
            $query = $query->where("id", $id_sub_tipo_expediente)->get();
            return TipoDerechoPeticionCollection::make($query);
        } elseif ($id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
            $query = TipoQuejaModel::query();
            $query = $query->where("id", $id_sub_tipo_expediente)->get();
            return TipoDerechoPeticionCollection::make($query);
        } elseif ($id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja']) {
            $query = TipoQuejaModel::query();
            $query = $query->where("id", $id_sub_tipo_expediente)->get();
            return TipoQuejaCollection::make($query);
        } elseif ($id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']) {
        }

        $query = TipoExpedienteModel::query();
        $query = $query->where("id", $id_tipo_expediente)->get();

        return TipoExpedienteCollection::make($query);
    }
}
