<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistroSeguimientoFormRequest;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Resources\RegistroSeguimiento\RegistroSeguimientoCollection;
use App\Http\Resources\RegistroSeguimiento\RegistroSeguimientoResource;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\RegistroSeguimientoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistroSeguimientoController extends Controller
{

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new RegistroSeguimientoModel());
    }

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
    public function store(RegistroSeguimientoFormRequest $request)
    {
        try {
            DB::connection()->beginTransaction();

            $datos = $request->validated()["data"]["attributes"];
            $datos['created_user'] = auth()->user()->name;

            $resultado = $this->repository->create($datos);

            if ($datos['finalizado'] == '1') {
                //ACTUALIZACION PROCESO
                ProcesoDiciplinarioModel::where('uuid', $datos['id_proceso_disciplinario'])->update(['estado' => 3]);
            }

            //INSERTAR EN LOG
            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datos['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

            //registramos log
            $logRequest['id_proceso_disciplinario'] = $datos['id_proceso_disciplinario'];
            $logRequest['id_etapa'] =  Constants::ETAPA['evaluacion'];
            $logRequest['id_fase'] = Constants::FASE['registro_seguimiento']; // antecedentes
            $logRequest['id_tipo_log'] = 2; // Log de tipo Fase
            $logRequest['descripcion'] = 'Se realiza Informe cierre';
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = 3; // Remisionado
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia; // Remisionado
            $logRequest['documentos'] = false;
            $logRequest['id_fase_registro'] = $resultado->uuid;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
            //INSERTAR EN LOG

            DB::connection()->commit();
            return RegistroSeguimientoResource::make($resultado);
        } catch (\Exception $e) {
            error_log($e);
            dd($e);
            // Woopsy
            DB::connection()->rollBack();
            if ((strpos($e->getMessage(), 'Network') !== false) || (strpos($e->getMessage(), 'Request Entity Too Large') !== false)) {

                $error['estado'] = false;
                $error['error'] = 'El archivo que está adjuntando es mayor de lo que permitido por el sistema.';

                return json_encode($error);
            } else {
                $error['estado'] = false;
                $error['error'] = 'No es posible realizar esta operación, si el problema persiste, comuníquese con el administrador.';

                return json_encode($error);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_proceso_disciplinario)
    {
        try {
            $respuesta_registro_seguimiento = $this->repository->customQuery(
                function ($model) use ($id_proceso_disciplinario) {
                    return $model
                        ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
                        ->orderBy('created_at', 'DESC')
                        ->get();
                }
            );

            return RegistroSeguimientoCollection::make($respuesta_registro_seguimiento);
        } catch (\Exception $e) {
            DB::connection()->rollBack();
            if ((strpos($e->getMessage(), 'Network') !== false) || (strpos($e->getMessage(), 'Request Entity Too Large') !== false)) {

                $error['estado'] = false;
                $error['error'] = 'El archivo que está adjuntando es mayor de lo que permitido por el sistema.';

                return json_encode($error);
            } else {
                $error['estado'] = false;
                $error['error'] = 'No es posible realizar esta operación, si el problema persiste, comuníquese con el administrador.';

                return json_encode($error);
            }
        }
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
