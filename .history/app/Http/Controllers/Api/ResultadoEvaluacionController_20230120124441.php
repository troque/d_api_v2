<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResultadoEvaluacionFormRequest;
use App\Http\Resources\ResultadoEvaluacion\ResultadoEvaluacionCollection;
use App\Http\Resources\ResultadoEvaluacion\ResultadoEvaluacionResource;
use App\Http\Utilidades\Constants;
use App\Models\EvaluacionModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\ResultadoEvaluacionModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultadoEvaluacionController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ResultadoEvaluacionModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = ResultadoEvaluacionModel::query();
        $query = $query->select('mas_resultado_evaluacion.*')->where('estado', true)->orderBy('mas_resultado_evaluacion.nombre', 'asc')->get();

        return ResultadoEvaluacionCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\ResultadoEvaluacionFormRequest  $request
     * @return \Illuminate\Http\Resources\ResultadoEvaluacionResource
     */
    public function store(ResultadoEvaluacionFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        $nombre = $datosRequest["nombre"];
        $consulta = DB::select("
                select * from mas_resultado_evaluacion
                where Translate(upper(nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu') = Translate(upper('" . $nombre . "'),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')
            ");
        DB::connection()->commit();

        if (count($consulta) >= 1) {

            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'Ya existe un registro con esta evaluacion';
            return json_encode($error);
        } else if (count($consulta) == 0) {

            try {

                $datosRequest = $request->validated()["data"]["attributes"];

                return ResultadoEvaluacionResource::make($this->repository->create($request->validated()["data"]["attributes"]));
            } catch (\Exception $e) {

                if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                    $error['estado'] = false;
                    $error['error'] = 'Ya existe un registro con esta evaluacion.';

                    return json_encode($error);
                }
            }
        } else {

            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'TENEMOS UN ERROR';
            return json_encode($error);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ResultadoEvaluacionResource::make($this->repository->find($id));
    }

    /**
     *
     */
    public function showAllEvaluaciones()
    {
        $query = ResultadoEvaluacionModel::query();
        $query = $query->select('mas_resultado_evaluacion.*')->orderBy('mas_resultado_evaluacion.nombre', 'asc')->get();
        return ResultadoEvaluacionCollection::make($query);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ResultadoEvaluacionFormRequest $request, $id)
    {
        return ResultadoEvaluacionResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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

    /**
     *
     */
    public function getResultadoEvaluacionHabilitados($id_proceso_disciplinario)
    {

        $proceso = ProcesoDiciplinarioModel::where("uuid", $id_proceso_disciplinario)
            ->where('id_tipo_proceso', '=', Constants::TIPO_DE_PROCESO['poder_preferente'])->get();

        $query = ResultadoEvaluacionModel::query();
        $evaluacion = EvaluacionModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)->where('estado_evaluacion', Constants::ESTADOS['activo'])->where('eliminado', false)->get();

        if (count($proceso) == 0 && count($evaluacion) > 0) {
            $query = $query->select('mas_resultado_evaluacion.*')->where('id', '<>', $evaluacion[0]->resultado_evaluacion)->where('estado', Constants::ESTADOS['activo'])->orderBy('mas_resultado_evaluacion.nombre', 'asc')->get();
            return ResultadoEvaluacionCollection::make($query);
        } else if (count($proceso) > 0) {
            $query = $query->select('mas_resultado_evaluacion.*')->where('id', '=', Constants::RESULTADO_EVALUACION['comisorio_eje'])->where('estado', Constants::ESTADOS['activo'])->get();
            return ResultadoEvaluacionCollection::make($query);
        }

        $query = $query->select('mas_resultado_evaluacion.*')->where('estado', Constants::ESTADOS['activo'])->orderBy('mas_resultado_evaluacion.nombre', 'asc')->get();
        return ResultadoEvaluacionCollection::make($query);
    }


    /**
     *
     */
    public function getNombreEvaluacion($id_evaluacion)
    {

        $query = ResultadoEvaluacionModel::query();
        $query = $query->where("id", $id_evaluacion)->select('mas_resultado_evaluacion.*')->get();

        return ResultadoEvaluacionCollection::make($query);
    }
}
