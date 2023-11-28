<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluacionFaseFormRequest;
use App\Http\Requests\TipoConductaFormRequest;
use App\Http\Resources\EvaluacionFase\EvaluacionFaseCollection;
use App\Http\Resources\EvaluacionFase\EvaluacionFaseResource;
use App\Models\EvaluacionFaseModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepartoController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new EvaluacionFaseModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = EvaluacionFaseModel::query();
        $query = $query->select('evaluacion_fase.id, evaluacion_fase.id_fase_actual, evaluacion_fase.id_fase_antecesora,
        evaluacion_fase.id_resultado_evaluacion, evaluacion_fase.id_tipo_expediente, evaluacion_fase.id_orden')->get();

        return EvaluacionFaseCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\TipoConductaFormRequest  $request
     * @return \Illuminate\Http\Resources\TipoConductaResource
     */
    public function store(TipoConductaFormRequest $request)
    {
        return EvaluacionFaseResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return EvaluacionFaseResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EvaluacionFaseFormRequest $request, $id)
    {
        return EvaluacionFaseResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->noContent();
    }


    public function showListaConfiguracion()
    {
        $query = $this->repository->customQuery(function ($model) {
            return $model->select('evaluacion_fase.id, evaluacion_fase.id_fase_actual, evaluacion_fase.id_fase_antecesora,
            evaluacion_fase.id_resultado_evaluacion, evaluacion_fase.id_tipo_expediente, evaluacion_fase.id_orden')->get();
        });

        return EvaluacionFaseCollection::make($query);
    }


    /**
     *
     */
    public function showListaTipoExpedienteEvaluacion()
    {
        $query = DB::select("select id_tipo_expediente,
                (select nombre from mas_tipo_expediente where id = id_tipo_expediente) as nombre_tipo_expediente,
                id_sub_tipo_expediente,
                (case
                WHEN id_tipo_expediente = 1 then (select nombre from mas_tipo_derecho_peticion where id = id_sub_tipo_expediente)
                WHEN id_tipo_expediente = 2 then (select nombre from mas_tipo_queja where id = id_sub_tipo_expediente)
                WHEN id_tipo_expediente = 3 then (select nombre from mas_tipo_queja where id = id_sub_tipo_expediente)
                WHEN id_tipo_expediente = 4 then (select nombre from mas_termino_respuesta where id = id_sub_tipo_expediente)
                END) as nombre_subtipo_expediente,
                id_resultado_evaluacion,
                (select nombre from mas_resultado_evaluacion where id = id_resultado_evaluacion) as nombre_resultado_evaluacion
                from evaluacion_fase
                group by id_tipo_expediente, id_sub_tipo_expediente, id_resultado_evaluacion
                order by id_tipo_expediente");

        for ($cont = 0; $cont < count($query); $cont++) {

            $json['type'] = 'evaluacion_fase';
            $json['attributes']['id_tipo_expediente'] = $query[$cont]->id_tipo_expediente;
            $json['attributes']['nombre_tipo_expediente'] = $query[$cont]->nombre_tipo_expediente;
            $json['attributes']['id_sub_tipo_expediente'] = $query[$cont]->id_sub_tipo_expediente;
            $json['attributes']['nombre_sub_tipo_expediente'] = $query[$cont]->nombre_subtipo_expediente;
            $json['attributes']['id_resultado_evaluacion'] = $query[$cont]->id_resultado_evaluacion;
            $json['attributes']['nombre_resultado_evaluacion'] = $query[$cont]->nombre_resultado_evaluacion;

            $reciboDatos['data'][$cont] = $json;
        }

        return json_encode($reciboDatos);
    }


    public function getFasesEtapaEvaluacionByIdExpedienteAndIdEvaluacion($id_tipo_expediente, $id_subtipo_expediente, $id_tipo_evaluacion)
    {
        $query = DB::select("select
                id_fase_actual,
                (select nombre from mas_fase where mas_fase.id = id_fase_actual) as nombre_fase_actual,
                id_fase_antecesora,
                (select nombre from mas_fase where mas_fase.id = id_fase_antecesora) as nombre_fase_antecesora
                from evaluacion_fase
                where id_tipo_expediente = " . $id_tipo_expediente . " and id_sub_tipo_expediente = " . $id_subtipo_expediente . " and id_resultado_evaluacion = " . $id_tipo_evaluacion . "
                order by orden");

        for ($cont = 0; $cont < count($query); $cont++) {

            $json['type'] = 'evaluacion_tipo_expediente';
            $json['attributes']['id_fase_actual'] = $query[$cont]->id_fase_actual;
            $json['attributes']['nombre_fase_actual'] = $query[$cont]->nombre_fase_actual;
            $json['attributes']['id_fase_antecesora'] = $query[$cont]->id_fase_antecesora;
            $json['attributes']['nombre_fase_antecesora'] = $query[$cont]->nombre_fase_antecesora;
            $reciboDatos['data'][$cont] = $json;
        }

        return json_encode($reciboDatos);
    }
}
