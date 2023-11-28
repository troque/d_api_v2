<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CambioEtapaFormRequest;
use App\Http\Resources\MisPendientes\MisPendientesCollection;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ProcesoDiciplinarioModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CambioEtapaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CambioEtapaFormRequest $request)
    {
        try {
            DB::connection()->beginTransaction();

            $datosRequest = $request->validated()["data"]["attributes"];

            //PROCESO DISCIPLINARIO
            //$procesoDisciplinario = ProcesoDiciplinarioModel::where('uuid');
            $logProcesoDisciplinario = LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['uuid_proceso_disciplinario'])
            ->orderby('created_at', 'desc')
            ->get();

            //dd($logProcesoDisciplinario[0]);

            // LOG PROCESO DISCIPLINARIO
            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['uuid_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

            $logRequest['id_proceso_disciplinario'] = $datosRequest['uuid_proceso_disciplinario'];
            $logRequest['id_etapa'] =  $datosRequest['id_etapa'];
            $logRequest['id_tipo_log'] = $logProcesoDisciplinario[0]['id_tipo_log']; // Log de tipo Etapa
            $logRequest['id_estado'] = $logProcesoDisciplinario[0]['id_estado'];
            $logRequest['descripcion'] = $datosRequest['justificacion'];
            $logRequest['id_dependencia_origen'] = $logProcesoDisciplinario[0]['id_dependencia_origen'];
            $logRequest['id_fase'] = $logProcesoDisciplinario[0]['id_fase'];
            $logRequest['id_funcionario_actual'] = $logProcesoDisciplinario[0]['id_funcionario_actual'];
            $logRequest['id_funcionario_asignado'] = $logProcesoDisciplinario[0]['id_funcionario_asignado'];
            $logRequest['id_funcionario_registra'] =  auth()->user()->name;
            $logRequest['id_tipo_transaccion'] = null;
            $logRequest['id_fase_registro'] = null;

            //$logModel = new LogProcesoDisciplinarioModel();
            $logModel = LogProcesoDisciplinarioModel::create($logRequest);


            $procesoDisciplinario = ProcesoDiciplinarioModel::where('uuid', $datosRequest['uuid_proceso_disciplinario'])->update(['id_etapa' => $datosRequest['id_etapa']]);

            //LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            DB::connection()->commit();
            return true;
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
        //
    }

    /**
     * Funcion para obtener un proceso disciplinario.
     */
    public function obtenerProcesoDisciplinario($radicado, $vigencia)
    {
        $query = ProcesoDiciplinarioModel::select(
                    'proceso_disciplinario.uuid',
                    'proceso_disciplinario.radicado',
                    'proceso_disciplinario.vigencia',
                    'proceso_disciplinario.id_tipo_proceso',
                    'proceso_disciplinario.estado',
                    'proceso_disciplinario.id_etapa',
                    'log_proceso_disciplinario.created_at',
                    'log_proceso_disciplinario.descripcion AS ultima_descripcion_log'
                )->distinct()
                ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                ->where('proceso_disciplinario.radicado', $radicado)
                ->where('proceso_disciplinario.vigencia', $vigencia)
                ->latest('log_proceso_disciplinario.created_at')
                ->get();

        if(count($query) > 0){
            return MisPendientesCollection::make($query)[0];
        }

        return MisPendientesCollection::make($query);
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

    /**
     * Funcion para obtener un proceso disciplinario.
     */
    public function activarProcesoDisciplinario(CambioEtapaFormRequest $request)
    {
        try {
            DB::connection()->beginTransaction();

            $datosRequest = $request->validated()["data"]["attributes"];

            //PROCESO DISCIPLINARIO
            //$procesoDisciplinario = ProcesoDiciplinarioModel::where('uuid');
            $logProcesoDisciplinario = LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['uuid_proceso_disciplinario'])
            ->orderby('created_at', 'desc')
            ->get();

            // LOG PROCESO DISCIPLINARIO
            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['uuid_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

            $logRequest['id_proceso_disciplinario'] = $datosRequest['uuid_proceso_disciplinario'];
            $logRequest['id_etapa'] =  $datosRequest['id_etapa'];
            $logRequest['id_tipo_log'] = $logProcesoDisciplinario[0]['id_tipo_log']; // Log de tipo Etapa
            $logRequest['id_estado'] = $logProcesoDisciplinario[0]['id_estado'];
            $logRequest['descripcion'] = $datosRequest['justificacion'];
            $logRequest['id_dependencia_origen'] = $logProcesoDisciplinario[0]['id_dependencia_origen'];
            $logRequest['id_fase'] = $logProcesoDisciplinario[0]['id_fase'];
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] =  auth()->user()->name;
            $logRequest['id_tipo_transaccion'] = null;
            $logRequest['id_fase_registro'] = null;

            $procesoDisciplinario = ProcesoDiciplinarioModel::where('uuid', $datosRequest['uuid_proceso_disciplinario'])->update(['estado' => Constants::ESTADO_PROCESO_DISCIPLINARIO['activo']]);
            
            //$logModel = new LogProcesoDisciplinarioModel();
            $logModel = LogProcesoDisciplinarioModel::create($logRequest);

            DB::connection()->commit();
            return true;
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
}
