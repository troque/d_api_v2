<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Controllers\Traits\PermisosExpedienteTrait;
use App\Http\Controllers\Traits\RepartoAleatorioParametrizadoTrait;
use App\Http\Requests\EvaluacionFormRequest;
use App\Http\Resources\Evaluacion\EvaluacionCollection;
use App\Http\Resources\Evaluacion\EvaluacionResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Resources\TipoConductaProcesoDisciplinario\TipoConductaProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\ClasificacionRadicadoModel;
use App\Models\DependenciaOrigenModel;
use App\Models\EvaluacionModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\TipoConductaProcesoDisciplinarioModel;
use App\Models\User;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class EvaluacionController extends Controller
{

    private $repository;
    use LogTrait;
    use RepartoAleatorioParametrizadoTrait;
    use PermisosExpedienteTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new EvaluacionModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = EvaluacionModel::query();
        $query = $query->select('evaluacion.*')->get();

        return EvaluacionCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EvaluacionFormRequest $request)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRecibidos = $request->validated();

            //buscamos el jefe de la dependencia
            $usuarioJefe = DependenciaOrigenModel::where("id", auth()->user()->id_dependencia)->first();

            if ($usuarioJefe->id_usuario_jefe) {

                //verificamos si el usuario esta activo para recibir casos
                $user = User::where("id", $usuarioJefe->id_usuario_jefe)->first();

                //si el usuario jefe de la dependencia destino puede recibir expedientes continuamos
                if ($user->reparto_habilitado == true) {

                    //buscamos que expedientes puede realizar el usuario
                    $siRecibe = false;

                    // Valida si el usuario jefe de la dependencia donde se enviara el proceso está habilitado para recibir este tipo de expediente
                    $permisos = $this->validarPermisosExpedienteById($usuarioJefe->id_usuario_jefe, $datosRecibidos["id_proceso_disciplinario"]);

                    if($permisos){
                        $siRecibe = true;
                    }
                    else{
                        $error['estado'] = false;
                        $error['error'] = 'El usuario destino ' . ($user->nombre . ' ' . $user->apellido . ' (' . $user->email . ')') . ' no tiene tipos de expedientes asociados para recibir casos';
                        return json_encode($error);
                    }

                    if ($siRecibe) {

                        $datosRecibidos['created_user'] = auth()->user()->name;
                        $evaluacion = EvaluacionResource::make($this->repository->create($datosRecibidos));
                        $array = json_decode(json_encode($evaluacion));

                        // REGISTRAR TIPO DE CONDUCTA
                        $tipoConductaRequest['id_proceso_disciplinario'] = $datosRecibidos["id_proceso_disciplinario"];
                        $tipoConductaRequest['id_tipo_conducta'] = $datosRecibidos["tipo_conducta"];
                        $tipoConductaRequest['estado'] = true;
                        $tipoConductaRequest['id_etapa'] = LogTrait::etapaActual($datosRecibidos['id_proceso_disciplinario']);;
                        $tipoConductaRequest['created_user'] = auth()->user()->name;
                        $tipoConductaModel = new TipoConductaProcesoDisciplinarioModel();
                        TipoConductaProcesoDisciplinarioResource::make($tipoConductaModel->create($tipoConductaRequest));

                        // REGISTRAR EN EL LOG
                        $logRequest['id_proceso_disciplinario'] = $datosRecibidos["id_proceso_disciplinario"];
                        $logRequest['id_etapa'] = LogTrait::etapaActual($datosRecibidos['id_proceso_disciplinario']);
                        $logRequest['id_fase'] =  Constants::FASE['evaluacion'];
                        $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
                        $logRequest['descripcion'] = 'Remitido a través de evaluación al usuario jefe de la dependencia';
                        $logRequest['id_estado'] = Constants::ESTADO_PROCESO_DISCIPLINARIO['remitido']; // Remisionado
                        $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
                        $logRequest['id_funcionario_actual'] = $user->name;
                        $logRequest['id_funcionario_registra'] = auth()->user()->name;
                        $logRequest['id_funcionario_asignado'] = $user->name;
                        $logRequest['id_fase_registro'] = $array->id;

                        $logModel = new LogProcesoDisciplinarioModel();
                        EvaluacionController::removerFuncionarioActualLog($datosRecibidos['id_proceso_disciplinario']);
                        LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

                    } else {
                        $error['estado'] = false;
                        $error['error'] = 'El usuario destino ' . ($user->nombre . ' ' . $user->apellido . ' (' . $user->email . ')') . ' no puede recibir este caso, revise
                                        que expedientes tiene habilitado el usuario o que tipo de expedientes esta intentando reasignar';
                        return json_encode($error);
                    }

                } else {
                    $error['estado'] = false;
                    $error['error'] = 'El usuario destino ' . ($user->nombre . ' ' . $user->apellido . ' (' . $user->email . ')') . ' no esta habilitado para recibir casos';
                    return json_encode($error);
                }

            } else {
                $error['estado'] = false;
                $error['error'] = 'La dependencia no tiene un usuario jefe asociado';
                return json_encode($error);
            }

            DB::connection()->commit();
            $respuesta['usuario_remitido'] = ($user->nombre . ' ' . $user->apellido . ' (' . $user->email . ')');
            return json_encode($respuesta);

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeEvalucionPoderPreferente(EvaluacionFormRequest $request)
    {
        try {

            error_log("ESTOY DENTRO DE EVALUACION DE PODER PREFERENTE");

            DB::connection()->beginTransaction();
            $datosRecibidos = $request->validated();

            // SE VALIDA QUE EL TIPO DE PROCESO DISCIPLINARIO
            $proceso_disciplinario = ProcesoDiciplinarioModel::where("uuid", $datosRecibidos["id_proceso_disciplinario"])->first();
            //buscamos el jefe de la dependencia

            if ($proceso_disciplinario->id_tipo_proceso == Constants::TIPO_DE_PROCESO['poder_preferente']) {

                //verificamos si el usuario esta activo para recibir casos
                $user = User::where("id",auth()->user()->id)->first();

                //si el usuario jefe de la dependencia destino puede recibir expedientes continuamos
                if ($user->reparto_habilitado == true) {

                    $datosRecibidos['created_user'] = auth()->user()->name;

                    /* CUANDO EL TIPO ES PODER PREFERENTE EL USUARIO QUE INICIO EL PROCESO LO FINALIZA.
                    /SIN EMBARGO UNA EVALUACION ES APROBADA CUANDO EL ESTADO ES APROBADO. POR TAL MOTIVO ASIGNAMOS DIRECTAMENTE APROBADO*/
                    $datosRecibidos['estado'] =  Constants::ESTADO_EVALUACION['aprobado_por_jefe'];

                    $evaluacion = EvaluacionResource::make($this->repository->create($datosRecibidos));
                    $array = json_decode(json_encode($evaluacion));

                    // REGISTRAR TIPO DE CONDUCTA
                    $tipoConductaRequest['id_proceso_disciplinario'] = $datosRecibidos["id_proceso_disciplinario"];
                    $tipoConductaRequest['id_tipo_conducta'] = $datosRecibidos["tipo_conducta"];
                    $tipoConductaRequest['estado'] = true;
                    $tipoConductaRequest['id_etapa'] = LogTrait::etapaActual($datosRecibidos['id_proceso_disciplinario']);;
                    $tipoConductaRequest['created_user'] = auth()->user()->name;
                    $tipoConductaModel = new TipoConductaProcesoDisciplinarioModel();
                    TipoConductaProcesoDisciplinarioResource::make($tipoConductaModel->create($tipoConductaRequest));

                    // REGISTRAR EN EL LOG
                    $logRequest['id_proceso_disciplinario'] = $datosRecibidos["id_proceso_disciplinario"];
                    $logRequest['id_etapa'] = LogTrait::etapaActual($datosRecibidos['id_proceso_disciplinario']);
                    $logRequest['id_fase'] =  Constants::FASE['evaluacion'];
                    $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
                    $logRequest['descripcion'] = 'Remitido a través de evaluación al usuario jefe de la dependencia';
                    $logRequest['id_estado'] = Constants::ESTADO_PROCESO_DISCIPLINARIO['remitido']; // Remisionado
                    $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
                    $logRequest['id_funcionario_actual'] = auth()->user()->name;
                    $logRequest['id_funcionario_registra'] = auth()->user()->name;
                    $logRequest['id_funcionario_asignado'] = auth()->user()->name;
                    $logRequest['id_fase_registro'] = $array->id;

                    $logModel = new LogProcesoDisciplinarioModel();
                    EvaluacionController::removerFuncionarioActualLog($datosRecibidos['id_proceso_disciplinario']);
                    LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

                } else {
                    $error['estado'] = false;
                    $error['error'] = 'El usuario destino ' . ($user->nombre . ' ' . $user->apellido . ' (' . $user->email . ')') . ' no esta habilitado para recibir casos';
                    return json_encode($error);
                }

            } else {
                $error['estado'] = false;
                $error['error'] = 'No existe este proceso disciplinario';
                return json_encode($error);
            }

            DB::connection()->commit();
            $respuesta['usuario_remitido'] = ($user->nombre . ' ' . $user->apellido . ' (' . $user->email . ')');
            return json_encode($respuesta);

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
     *
     */
    public function getAllEvalucionByIdProcesoDisciplinario($procesoDiciplinarioUUID)
    {
        error_log($procesoDiciplinarioUUID);
        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID) {
            return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)->orderBy('created_at', 'desc')->get();
        });
        return EvaluacionCollection::make($query);
    }

    /**
     *
     */
    public function crearEvaluacionRemitida(EvaluacionFormRequest $request)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRecibidos = $request->validated();
            error_log(json_encode($datosRecibidos));
            //buscamos el usuario
            $evaluacion = EvaluacionModel::where("id_proceso_disciplinario", $datosRecibidos['id_proceso_disciplinario'])->where("estado_evaluacion",true)->first();

            error_log(json_encode($evaluacion));

            $user = $this->repartoAleatorioParametrizado($datosRecibidos['id_proceso_disciplinario'], 0, 0, "", null, $evaluacion->created_user, null, false);
            //$user = User::where("name", $evaluacion->created_user)->first();
            //dd($user);
            if(!$user->estado){
                $error = new stdClass;
                $error->estado = false;
                $error->error = $user->error;
                return $error;
            }

            if ($evaluacion && $user) {

                //Actualiza las actuales evaluaciones como inactivas.
                EvaluacionModel::where('id_proceso_disciplinario', $datosRecibidos['id_proceso_disciplinario'])->update(['estado_evaluacion' => 0]);

                // Se registra la información en evaluacion
                $datosRecibidos['noticia_priorizada'] = $evaluacion["noticia_priorizada"];
                $datosRecibidos['resultado_evaluacion'] = ( $datosRecibidos["resultado_evaluacion"] ? $datosRecibidos["resultado_evaluacion"] : $evaluacion["resultado_evaluacion"]);
                $datosRecibidos['tipo_conducta'] = $evaluacion["tipo_conducta"];
                $datosRecibidos['created_user'] = auth()->user()->name;
                $datosRecibidos['estado_evaluacion'] = true;
                $query = EvaluacionResource::make($this->repository->create($datosRecibidos));
                $array = json_decode(json_encode($query));

                $logModel = new LogProcesoDisciplinarioModel();
                $logRequest['id_proceso_disciplinario'] = $evaluacion["id_proceso_disciplinario"];
                $logRequest['id_etapa'] = Constants::ETAPA['evaluacion'];
                $logRequest['id_fase'] = Constants::FASE['evaluacion'];
                $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa'];
                $logRequest['descripcion'] =  $datosRecibidos['justificacion'];
                $logRequest['id_estado'] = Constants::ESTADO_PROCESO_DISCIPLINARIO['remitido'];
                $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
                $logRequest['id_funcionario_actual'] = $user->id_funcionario_actual;
                $logRequest['id_funcionario_asignado'] = $user->id_funcionario_actual;
                $logRequest['id_funcionario_registra'] = auth()->user()->name;
                $logRequest['id_fase_registro'] = $array->id;
                EvaluacionController::removerFuncionarioActualLog($datosRecibidos['id_proceso_disciplinario']);

                LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
            }


            $respuesta['usuario_remitido'] = ($user->nombre_completo . ' ' . $user->apellido_completo . ' (' . $user->email . ')');
            DB::connection()->commit();

            return json_encode($respuesta);

        } catch (\Exception $e) {
            error_log($e);
            dd($e);
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
        return EvaluacionResource::make($this->repository->find($id));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EvaluacionFormRequest $request, $id)
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
     * Update Tipo de conducta
     *
     */
    public function updateTipoConducta(EvaluacionFormRequest $request)
    {

        $datosRequest = $request->validated();
        $evaluacion = EvaluacionModel::where("id_proceso_disciplinario", $datosRequest['id_proceso_disciplinario'])->where('estado_evaluacion', Constants::ESTADOS['activo'])->get();

        if($evaluacion!=null){

            // SE ACTUALIZA LOS ESTADOS DEL ACTUAL RESGITRO ACTIVO DE EVALUACION
            $datosRequest['noticia_priorizada'] = $evaluacion[0]->noticia_priorizada;
            $datosRequest['justificacion'] = $evaluacion[0]->justificacion;
            $datosRequest['resultado_evaluacion'] = $evaluacion[0]->resultado_evaluacion;
            $datosRequest['created_user'] = auth()->user()->name;
            $datosRequest['estado_evaluacion'] = true;

            $query = EvaluacionResource::make($this->repository->create($datosRequest));
            $array = json_decode(json_encode($query));

            EvaluacionModel::where("uuid", $evaluacion[0]->uuid)->update(['estado_evaluacion' => Constants::ESTADOS['inactivo']]);

            // TRAER EL ULTIMO LOG REGSITRADO DEL PROCESO DISCIPLINARIO
            $ultimo_log = LogProcesoDisciplinarioModel::where("id_proceso_disciplinario", $datosRequest['id_proceso_disciplinario'])->where('id_funcionario_actual', '<>', null)->get();

            $logModel = new LogProcesoDisciplinarioModel();
            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = $ultimo_log[0]->id_etapa;
            $logRequest['id_fase'] = $ultimo_log[0]->id_fase;
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa'];
            $logRequest['descripcion'] =  "Se actualizo el tipo de conducta";
            $logRequest['id_estado'] = Constants::ESTADO_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['id_funcionario_actual'] = $evaluacion[0]->created_user;
            $logRequest['id_funcionario_asignado'] = $evaluacion[0]->created_user;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_fase_registro'] = $array->id;
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->orderBy('created_at', 'desc')->get();
            });

            return EvaluacionCollection::make($query);
        }
    }

     /**
     * OBTENER TIPO DE PROCESO DISCIPLINARIO
     */
    public function getEstadoEvaluacion($id_proceso_disciplinario){

        // Se valida la dependencia en la que se encuentra el usuario en sesión
        $evaluacion_aprobado = EvaluacionModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)->where('estado', Constants::ESTADO_EVALUACION['aprobado_por_jefe'])->get();
        $evaluacion_rechazado= EvaluacionModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)->where('estado', Constants::ESTADO_EVALUACION['rechazado_por_jefe'])->get();
        $evaluacion_recibido= EvaluacionModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)->where('estado', Constants::ESTADO_EVALUACION['registrado'])->get();

       if(count($evaluacion_aprobado)>0){
            error_log("evaluacion_aprobado");
            $reciboDatos['estado_evaluacion'] = $evaluacion_aprobado[0]->estado;
            $json['data']['attributes'] = $reciboDatos;
            return json_encode($json);
       }

       if(count($evaluacion_rechazado)>0){
            error_log("evaluacion_rechazado");
            $reciboDatos['estado_evaluacion'] = $evaluacion_rechazado[0]->estado;
            $json['data']['attributes'] = $reciboDatos;
            return json_encode($json);
        }

        if(count($evaluacion_recibido)>0){
            error_log("evaluacion_recibido");
            $reciboDatos['estado_evaluacion'] = $evaluacion_recibido[0]->estado;
            $json['data']['attributes'] = $reciboDatos;
            return json_encode($json);
        }

        $reciboDatos['estado_evaluacion'] = 0;
        $json['data']['attributes'] = $reciboDatos;
        return json_encode($json);

   }
}
