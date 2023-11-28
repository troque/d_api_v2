<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Requests\ComunicacionInteresadoFormRequest;
use App\Http\Resources\ComunicacionInteresado\ComunicacionInteresadoCollection;
use App\Http\Resources\ComunicacionInteresado\ComunicacionInteresadoResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\ComunicacionInteresadoModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComunicacionInteresadoController extends Controller
{
    private $repository;
    use LogTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ComunicacionInteresadoModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\DiasNoLaboralesFormRequest  $request
     * @return App\Http\Resources\DiasNoLaborales\DiasNoLaboralesResource
     */
    public function store(ComunicacionInteresadoFormRequest $request)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRequest = $request->validated()["data"]["attributes"];
            $concatenado = $datosRequest['concatenado'];
            $datosRequest['eliminado'] = false;

            foreach ($concatenado as $r) {

                $pieces = explode("|", $r);
                $datosRequest['estado'] = 1;
                if (!$this->yaEstaAsociado($pieces[1], $concatenado)) {
                    $datosRequest['id_interesado'] = $pieces[0];
                    $datosRequest['id_documento_sirius'] = $pieces[1];
                    $datosRequest['created_user'] = auth()->user()->name;

                    $respuesta = ComunicacionInteresadoResource::make($this->repository->create($datosRequest));
                    $array = json_decode(json_encode($respuesta));

                    $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
                    $logRequest['id_etapa'] =  LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
                    $logRequest['id_fase'] = Constants::FASE['comunicacion_interesado'];
                    $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
                    $logRequest['descripcion'] = "";
                    $logRequest['created_user'] = auth()->user()->name;
                    $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
                    $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
                    $logRequest['documentos'] = false;
                    $logRequest['id_fase_registro'] = $array->id;
                    $logRequest['id_funcionario_actual'] = auth()->user()->name;
                    $logRequest['id_funcionario_registra'] = auth()->user()->name;
                    $logRequest['id_funcionario_asignado'] = auth()->user()->name;
                    $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['ninguno'];
                    ComunicacionInteresadoController::removerFuncionarioActualLog($datosRequest['id_proceso_disciplinario']);

                    $logModel = new LogProcesoDisciplinarioModel();
                    LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
                } else {
                    DB::connection()->rollBack();
                    $error['estado'] = true;
                    $error['error'] = "Uno o más radicados ya se encuentran asociados a más de un interesado";
                    return json_encode($error);
                }
            }

            //validamos que un radicado no este relacionado con otros interesados

            DB::connection()->commit();
            return $respuesta;
        } catch (\Exception $e) {
            // Woopsy
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    public function yaEstaAsociado($idRadicado, $concatenado)
    {
        $yaestaAsociado = false;
        $contador = 0;
        foreach ($concatenado as $r) {

            $pieces = explode("|", $r);

            if ($idRadicado == $pieces[1]) {
                $contador++;
            }
        }

        if ($contador > 1) {
            return true;
        }

        return false;
    }


    public function getComunicacionInteresadoByProcesoDisciplinario($procesoDiciplinarioUUID, ComunicacionInteresadoFormRequest $request)
    {
        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID) {
            return $model->where('comunicacion_interesado.id_proceso_disciplinario', $procesoDiciplinarioUUID)
                ->where('eliminado', false)
                ->leftJoin('interesado', 'comunicacion_interesado.id_interesado', '=', 'interesado.uuid')
                ->leftJoin('DOCUMENTO_SIRIUS', 'comunicacion_interesado.id_documento_sirius', '=', 'DOCUMENTO_SIRIUS.uuid')
                ->select(
                    'comunicacion_interesado.uuid',
                    'comunicacion_interesado.id_interesado',
                    'comunicacion_interesado.id_documento_sirius',
                    'comunicacion_interesado.id_proceso_disciplinario',
                    'comunicacion_interesado.estado',
                    'comunicacion_interesado.eliminado',
                    'interesado.primer_nombre AS primer_nombre',
                    'interesado.segundo_nombre AS segundo_nombre',
                    'interesado.primer_apellido AS primer_apellido',
                    'interesado.segundo_apellido AS segundo_apellido',
                    'DOCUMENTO_SIRIUS.NUM_RADICADO  as radicado',
                    'DOCUMENTO_SIRIUS.nombre_archivo as archivo'
                )->orderBy('comunicacion_interesado.CREATED_AT', 'desc')->get();
        });

        return ComunicacionInteresadoCollection::make($query);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ComunicacionInteresadoFormRequest $request,  $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
