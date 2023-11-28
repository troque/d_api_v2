<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrdenFuncionarioRequest;
use App\Http\Resources\OrdenFuncionario\OrdenFuncionarioCollection;
use App\Models\OrdenFuncionarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenFuncionarioController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new OrdenFuncionarioModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*$respuesta = $this->repository->customQuery(
            function ($model){
                return $model
                ->orderby('created_at', 'desc')
                ->get();
            }
        );

        if($respuesta->count() > 0){
            $respuesta = $respuesta->first();
            $respuesta = $this->repository->customQuery(
                function ($model) use ($respuesta){
                    return $model
                    ->where('grupo', $respuesta->grupo)
                    ->orderby('orden', 'asc')
                    ->get();
                }
            );
            return OrdenFuncionarioCollection::make($respuesta);
        }
        else{
            return OrdenFuncionarioCollection::make($respuesta);
        }*/

        $respuesta = $this->repository->customQuery(
            function ($model) {
                return $model
                    ->orderby('created_at', 'desc')
                    ->get();
            }
        );

        return OrdenFuncionarioCollection::make($respuesta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrdenFuncionarioRequest $request)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRecibidos = $request->validated()["data"]["attributes"];

            for ($cont = 0; $cont < count($datosRecibidos); $cont++) {

                $datosRecibidos[$cont]['estado'] = true;
                $datosRecibidos[$cont]['funcionario_siguiente'] = 0;
                $datosRecibidos[$cont]['created_user'] = auth()->user()->name;

                if ($cont > 0) {
                    $datosRecibidos[$cont]['grupo'] = $datosRecibidos[0]['grupo'];
                } else {
                    $datosRecibidos[$cont]['grupo'] = -1;
                }

                $respuesta = $this->repository->create($datosRecibidos[$cont]);
                $datosRecibidos[$cont]['id'] = $respuesta->id;

                if ($cont == 0) {
                    $this->repository->update(['grupo' => $respuesta->id], $respuesta->id);
                    $datosRecibidos[$cont]['grupo'] = $respuesta->id;
                } else if ($cont > 0 && $cont < count($datosRecibidos)) {
                    $datosRecibidos[$cont]['funcionario_siguiente'] = $datosRecibidos[$cont - 1]['id'];
                    OrdenFuncionarioModel::where('id', $datosRecibidos[$cont - 1]['id'])->update(['funcionario_siguiente' => $datosRecibidos[$cont]['id']]);
                }
            }
            DB::connection()->commit();
            return $respuesta;
        } catch (QueryException  $e) {

            $error['estado'] = false;
            $error['error'] = $e->getMessage();
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
        /*$respuesta = $this->repository->customQuery(
            function ($model) use ($id){
                return $model
                ->where('id_evaluacion',$id)
                ->orderby('created_at', 'desc')
                ->get();
            }
        );

        if($respuesta->count() > 0){
            $respuesta = $respuesta->first();
            $respuesta = $this->repository->customQuery(
                function ($model) use ($respuesta, $id){
                    return $model
                    ->where('grupo', $respuesta->grupo)
                    ->where('id_evaluacion', $id)
                    ->orderby('orden', 'asc')
                    ->get();
                }
            );
            return OrdenFuncionarioCollection::make($respuesta);
        }
        else{
            return OrdenFuncionarioCollection::make($respuesta);
        }*/
    }

    public function showListaRoles($id_evaluacion, $id_expediente, $id_sub_expediente, $id_tercer_expediente)
    {

        if ($id_tercer_expediente == 'true') {
            $id_tercer_expediente = 1;
        } else {
            $id_tercer_expediente = 0;
        }

        $respuesta = $this->repository->customQuery(
            function ($model) use ($id_evaluacion, $id_expediente, $id_sub_expediente, $id_tercer_expediente) {
                return $model
                    ->where('id_evaluacion', $id_evaluacion)
                    ->where('id_expediente', $id_expediente)
                    ->where(function ($model) use ($id_sub_expediente) {
                        $model->where('id_sub_expediente', $id_sub_expediente)
                            ->orWhereNull('id_sub_expediente');
                    })
                    ->where(function ($model) use ($id_tercer_expediente) {
                        $model->where('id_tercer_expediente', $id_tercer_expediente)
                            ->orWhereNull('id_tercer_expediente');
                    })
                    ->orderby('created_at', 'desc')
                    ->get();
            }
        );

        if ($respuesta->count() > 0) {
            $respuesta = $respuesta->first();
            $respuesta = $this->repository->customQuery(
                function ($model) use ($respuesta) {
                    return $model
                        ->where('grupo', $respuesta->grupo)
                        ->orderby('orden', 'asc')
                        ->get();
                }
            );

            return OrdenFuncionarioCollection::make($respuesta);
        } else {
            return OrdenFuncionarioCollection::make($respuesta);
        }

        /*$query_sub_expediente = '';

        if($id_sub_expediente != "null"){
            $query_sub_expediente = "id_sub_expediente = $id_sub_expediente";
        }
        else{
            $query_sub_expediente = 'id_sub_expediente IS NULL';
        }

        $query_tercer_expediente = '';

        if($id_tercer_expediente != "null"){
            $query_tercer_expediente = "id_tercer_expediente = $id_sub_expediente";
        }
        else{
            $query_tercer_expediente = 'id_tercer_expediente IS NULL';
        }

        $respuesta_grupo = "
            SELECT
                MAX(grupo)
            FROM mas_orden_funcionario
            WHERE id_evaluacion = $id_evaluacion
            AND id_expediente = $id_expediente
            AND $query_sub_expediente
            AND $query_tercer_expediente
        ";

        $respuesta = DB::select("
            SELECT
                mas_orden_funcionario.id,
                mas_orden_funcionario.id_funcionario,
                mas_orden_funcionario.orden,
                mas_orden_funcionario.grupo,
                mas_orden_funcionario.estado,
                mas_orden_funcionario.id_evaluacion,
                mas_orden_funcionario.id_expediente,
                mas_orden_funcionario.id_sub_expediente,
                mas_orden_funcionario.id_tercer_expediente,
                mas_orden_funcionario.funcionario_siguiente,
                mas_orden_funcionario.created_at,
                r.name AS nombre
            FROM mas_orden_funcionario
            INNER JOIN roles r ON r.id = mas_orden_funcionario.id_funcionario
            WHERE id_evaluacion = $id_evaluacion
            AND id_expediente = $id_expediente
            AND $query_sub_expediente
            AND $query_tercer_expediente
            AND grupo = ($respuesta_grupo)
        ");

        return OrdenFuncionarioCollection::make($respuesta);*/

        /*$respuesta = $this->repository->customQuery(
            function ($model) use ($id_evaluacion, $id_expediente, $id_sub_expediente, $id_tercer_expediente){
                return $model
                ->where('id_evaluacion',$id_evaluacion)
                ->where('id_expediente',$id_expediente)
                ->where('id_sub_expediente',$id_sub_expediente)
                ->where('id_tercer_expediente',$id_tercer_expediente)
                ->orderby('created_at', 'desc')
                ->get();
            }
        );

        dd($respuesta);

        if($respuesta->count() > 0){
            $respuesta = $respuesta->first();
            $respuesta = $this->repository->customQuery(
                function ($model) use ($respuesta, $id_evaluacion, $id_expediente, $id_sub_expediente, $id_tercer_expediente){
                    return $model
                    ->where('grupo', $respuesta->grupo)
                    ->orderby('orden', 'asc')
                    ->get();
                }
            );
            return OrdenFuncionarioCollection::make($respuesta);
        }
        else{
            return OrdenFuncionarioCollection::make($respuesta);
        }*/
    }

    public function showHistorico($id_evaluacion, $id_expediente, $id_sub_expediente, $id_tercer_expediente)
    {

        if ($id_tercer_expediente == 'true') {
            $id_tercer_expediente = 1;
        } else {
            $id_tercer_expediente = 0;
        }

        $respuesta = $this->repository->customQuery(
            function ($model) use ($id_evaluacion, $id_expediente, $id_sub_expediente, $id_tercer_expediente) {
                return $model
                    ->where('id_evaluacion', $id_evaluacion)
                    ->where('id_expediente', $id_expediente)
                    ->where(function ($model) use ($id_sub_expediente) {
                        $model->where('id_sub_expediente', $id_sub_expediente)
                            ->orWhereNull('id_sub_expediente');
                    })
                    ->where(function ($model) use ($id_tercer_expediente) {
                        $model->where('id_tercer_expediente', $id_tercer_expediente)
                            ->orWhereNull('id_tercer_expediente');
                    })
                    ->orderby('created_at', 'desc')
                    ->get();
            }
        );

        $collection_funcionarios = OrdenFuncionarioCollection::make($respuesta);

        $merged = $collection_funcionarios->sortByDesc('fecha_registro')->groupBy('grupo')->all();

        return $merged;
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
}
