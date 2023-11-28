<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TempProcesoDisciplinarioFormRequest;
use App\Http\Resources\TempProcesoDisciplinario\TempProcesoDisciplinarioCollection;
use App\Http\Resources\TempProcesoDisciplinario\TempProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\TempProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TempProcesoDisciplinarioController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {

        $this->repository = $repository;
        $this->repository->setModel(new TempProcesoDisciplinarioModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TempProcesoDisciplinarioCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TempProcesoDisciplinarioFormRequest $request)
    {
        try {

            $datosRequest = $request->validated()["data"]["attributes"];

            if ($datosRequest['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['correspondencia_sirius']) {

                $datosRequest['radicado'] = '2022-ER-0119316';

                if (!preg_match("/^\d{4}-[A-Za-z]{2}-\d{7}$/", $datosRequest['radicado'])) {
                    $error['estado'] = false;
                    $error['error'] = 'El radicado no cumple con el formato de un código SIRIUS';
                    return json_encode($error);
                }
            }

            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model->where('radicado', $datosRequest['radicado'])
                    ->where('vigencia', $datosRequest['vigencia'])
                    ->get();
            });

            if (count($query) == 0) {
                $respuesta = TempProcesoDisciplinarioResource::make($this->repository->create($datosRequest));
            } else {

                $respuesta = TempProcesoDisciplinarioModel::where('radicado', $datosRequest['radicado'])
                    ->where('vigencia', $datosRequest['vigencia'])
                    ->update([
                        'estado' => $datosRequest['estado'],
                        'id_tipo_proceso' => $datosRequest['id_tipo_proceso'],
                        'id_dependencia_origen' => $datosRequest['id_dependencia_origen'],
                        'id_dependencia_duena' => $datosRequest['id_dependencia_duena'],
                        'id_etapa' => $datosRequest['id_etapa'],
                        'created_user' => auth()->user()->name,
                        'created_at' => $datosRequest['fecha_registro'],
                        'id_tipo_expediente' => $datosRequest['id_tipo_expediente'],
                        'id_sub_tipo_expediente' => $datosRequest['id_sub_tipo_expediente'],
                        'id_tipo_evaluacion' => $datosRequest['id_tipo_evaluacion'],
                        'id_tipo_conducta' => $datosRequest['id_tipo_conducta'],
                        'radicado_padre_desglose' => $datosRequest['radicado_padre_desglose'],
                        'vigencia_padre_desglose' => $datosRequest['vigencia_padre_desglose'],
                        'auto_desglose' => $datosRequest['auto_desglose'],
                        'usuario_actual' => $datosRequest['usuario_actual'],
                    ]);
            }

            return $respuesta;
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
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
        return TempProcesoDisciplinarioResource::make($this->repository->find($id));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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


    public function getTempProceso($radicado, $vigencia)
    {

        $query = $this->repository->customQuery(function ($model) use ($radicado, $vigencia) {
            return $model->where('radicado', $radicado)
                ->where('vigencia', $vigencia)
                ->get();
        });

        return TempProcesoDisciplinarioCollection::make($query);
    }
}
