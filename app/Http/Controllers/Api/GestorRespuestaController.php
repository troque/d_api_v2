<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ProcesoDisciplinarioTrait;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Controllers\Traits\NumeroCasosTrait;
use App\Http\Controllers\Traits\PreRepartoAleatorioTrait;
use App\Http\Controllers\Traits\RepartoAleatorioParametrizadoTrait;
use App\Http\Controllers\Traits\SiriusTrait;
use App\Http\Requests\GestorRespuestaDocumentoFormRequest;
use App\Http\Requests\GestorRespuestaFormRequest;
use App\Http\Resources\GestorRespuesta\GestorRespuestaCollection;
use App\Http\Resources\GestorRespuesta\GestorRespuestaResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\DependenciaOrigenModel;
use App\Models\DocumentoSiriusModel;
use App\Models\GestorRespuestaModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\OrdenFuncionarioModel;
use App\Models\ResultadoEvaluacionModel;
use App\Models\TbintDocumentoSiriusDescripcionModel;
use App\Models\User;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class GestorRespuestaController extends Controller
{
    private $repository;
    use LogTrait;
    use PreRepartoAleatorioTrait;
    use RepartoAleatorioParametrizadoTrait;
    use SiriusTrait;
    use ProcesoDisciplinarioTrait;
    use NumeroCasosTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new GestorRespuestaModel());
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
    public function store(GestorRespuestaFormRequest $request)
    {
        try {
            DB::connection()->beginTransaction();

            //Se establecen variables de incio
            $datos_gestor_respuesta = $request->validated()["data"]["attributes"];
            $datos_gestor_respuesta['eliminado'] = false;
            $datos_gestor_respuesta['proceso_finalizado'] = false;
            $dirigido_a = null;
            $datos_gestor_respuesta['orden_funcionario'] = 0;
            $reparto = null;
            $ultimo_usuario = false;

            $datos_gestor_respuesta['id_etapa'] = Constants::ETAPA['evaluacion'];
            $datos_gestor_respuesta['id_fase'] = Constants::FASE['gestor_respuesta'];

            $query_max_version = $this->repository->customQuery(
                function ($model) use ($datos_gestor_respuesta) {
                    return $model
                        ->where('id_proceso_disciplinario', $datos_gestor_respuesta['id_proceso_disciplinario'])
                        ->where('eliminado', false)
                        ->max('version');
                }
            );

            $query_gestor_respuesta = $this->repository->customQuery(
                function ($model) use ($datos_gestor_respuesta, $query_max_version) {
                    return $model
                        ->where('id_proceso_disciplinario', $datos_gestor_respuesta['id_proceso_disciplinario'])
                        ->where('eliminado', false)
                        ->where('version', $query_max_version)
                        ->orderby('created_at', 'desc')
                        ->get();
                }
            );

            $datos_gestor_respuesta['id_documento_sirius'] = $query_gestor_respuesta[0]->id_documento_sirius;

            //Se enlista modelo del Orden de los Funcionarios
            $repository_compulsa = new RepositoryGeneric();
            $repository_compulsa->setModel(new OrdenFuncionarioModel());

            if ($query_max_version) {
                //Obtiene la lista completa de los tipos de funcionarios, dada la configuración anterior
                $respuesta_funcionarios = $repository_compulsa->customQuery(
                    function ($model) use ($query_gestor_respuesta, $datos_gestor_respuesta) {
                        return $model
                            ->where('grupo', $query_gestor_respuesta[0]->id_mas_orden_funcionario)
                            ->where('id_evaluacion', $datos_gestor_respuesta['id_tipo_evaluacion']) //TIPO DE EVALUACION
                            ->orderby('orden', 'asc')
                            ->get();
                    }
                )->all();
            } else { //Se obtiene el ulitmo registro de la configuración vigente, para obtener la configuración a asignar, en caso de que no exista un registro en Gestor Respuesta

                $respuesta_ultima_configuracion = $repository_compulsa->customQuery(
                    function ($model) {
                        return $model
                            ->orderby('created_at', 'desc')
                            ->get();
                    }
                );

                //Obtiene la lista completa de los tipos de funcionarios, dada la configuración anterior
                if ($respuesta_ultima_configuracion->count() > 0) {
                    $respuesta_ultima_configuracion = $respuesta_ultima_configuracion->first();
                    $respuesta_funcionarios = $repository_compulsa->customQuery(
                        function ($model) use ($respuesta_ultima_configuracion) {
                            return $model
                                ->where('grupo', $respuesta_ultima_configuracion->grupo)
                                ->orderby('orden', 'asc')
                                ->get();
                        }
                    )->all();
                } else {
                    //throw new NotFoundHttpException('Lista de parametrización de usuarios no encontrada, si el error persiste comuníquese con el administrador');
                    $error = new stdClass;
                    $error->estado = false;
                    $error->error = 'LISTA DE PARAMETRIZACIÓN DE USUARIOS NO ENCONTRADA, SI EL ERROR PERSISTE COMUNÍQUESE CON EL ADMINISTRADOR.';
                    return $error;
                }
            }

            //Se valida que exista un proceso iniciado
            if ($query_max_version) {
                if ($datos_gestor_respuesta['aprobado']) {
                    $datos_gestor_respuesta['orden_funcionario'] = count($query_gestor_respuesta);

                    for ($cont = 0; $cont < count($respuesta_funcionarios); $cont++) {
                        if ($respuesta_funcionarios[$cont]->orden == $datos_gestor_respuesta['orden_funcionario']) {
                            if ($cont + 3 >= count($respuesta_funcionarios)) {
                                $ultimo_usuario = true;
                            }
                        }
                    }

                    if ($ultimo_usuario) {
                        $datos_gestor_respuesta['proceso_finalizado'] = true;
                        $datos_gestor_respuesta['id_mas_orden_funcionario'] = $respuesta_funcionarios[0]['grupo'];

                        //PREGUNTA SI EL ROL QUE TIENE QUE BUSCAR ES JEFE
                        $respuesta_jefe = DB::select(
                            "
                            SELECT
                                r.id,
                                r.name
                            FROM
                            roles r
                            INNER JOIN funcionalidad_rol fr ON r.id = fr.role_id
                            INNER JOIN mas_funcionalidad mf ON fr.funcionalidad_id = mf.id
                            WHERE r.id = " . $datos_gestor_respuesta['reparto']['id_funcionalidad'] . "
                            AND mf.nombre_mostrar = '" . Constants::FUNCIONALIDAD_ROL['jefe'] . "'"
                        );

                        if (count($respuesta_jefe) > 0) {
                            //BUSCAR JEFE DE LA DEPENDENCIA
                            $repository_dependencia_origen = new RepositoryGeneric();
                            $repository_dependencia_origen->setModel(new DependenciaOrigenModel());
                            $resultado_dependencia = $repository_dependencia_origen->find(auth()->user()->id_dependencia);

                            if ($resultado_dependencia->id_usuario_jefe == null) {
                                $error['estado'] = false;
                                $error['error'] = 'NO ES POSIBLE COMPLETAR EL PROCEDIMIENTO, LA DEPENDENCIA ACTUAL NO TIENE USUARIO JEFE ASIGNADO';

                                return json_encode($error);
                            }

                            //BUSCAR USUARIO
                            $repository_usuario = new RepositoryGeneric();
                            $repository_usuario->setModel(new User());
                            $resultado_usuario = $repository_usuario->find($resultado_dependencia->id_usuario_jefe);

                            $reparto = new stdClass;
                            $reparto->nombre_funcionario = $resultado_usuario->name;
                            $reparto->nombre_completo = $resultado_usuario->nombre;
                            $reparto->apellido_completo = $resultado_usuario->apellido;
                            $reparto->id_dependencia_origen = auth()->user()->id_dependencia;
                            $reparto->id_funcionario_asignado = $resultado_usuario->name;
                            $reparto->estado = true;
                        } else {
                            $reparto = $this->repartoAleatorioParametrizado(
                                $datos_gestor_respuesta['id_proceso_disciplinario'],
                                $datos_gestor_respuesta['id_etapa'],
                                $datos_gestor_respuesta['id_fase'],
                                $datos_gestor_respuesta['descripcion'],
                                $datos_gestor_respuesta['reparto']['id_funcionalidad'],
                                $datos_gestor_respuesta['reparto']['id_funcionario_asignado'],
                                'E_GestorRespuesta',
                                true,
                                null
                            );
                        }
                    } else {
                        $datos_gestor_respuesta['nuevo_documento'] = false;
                        $datos_gestor_respuesta['id_mas_orden_funcionario'] = $respuesta_funcionarios[$datos_gestor_respuesta['orden_funcionario']]->grupo;
                    }
                } else {
                    //AQUI REQUIERE REPARTO DIRIGDO AL PRIMER USUARIO
                    $datos_gestor_respuesta['version'] = $query_max_version;
                    $datos_gestor_respuesta['nuevo_documento'] = false;

                    $resultado_expediente = $this->obtenerDatosProcesoDisciplinario($datos_gestor_respuesta['id_proceso_disciplinario']);

                    if ($resultado_expediente->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']) { //Si es tutela, se realiza reparto prioritario al funcional seleccionado por el usuario
                        $dirigido_a = $datos_gestor_respuesta['reparto']['id_funcionario_asignado'];
                    } else { //De lo contrario, se realiza el reparto al usuario inicial del documento
                        $dirigido_a = $query_gestor_respuesta[$query_gestor_respuesta->count() - 1]['created_user'];
                    }
                    $id_funcionario = $respuesta_funcionarios[0]['id_funcionario'];
                    $datos_gestor_respuesta['id_mas_orden_funcionario'] = $respuesta_funcionarios[0]['grupo'];
                }
            } else { //Si no existe el proceso, comienza uno nuevo
                $datos_gestor_respuesta['id_mas_orden_funcionario'] = $respuesta_funcionarios[0]->grupo;
                $datos_gestor_respuesta['orden'] = 1;
                $datos_gestor_respuesta['nuevo_documento'] = true;
                $id_funcionario = $respuesta_funcionarios[0]['id_funcionario'];
            }

            $datos_gestor_respuesta['created_user'] = auth()->user()->name;

            //REPARTO ALEATORIO
            if (!$datos_gestor_respuesta['proceso_finalizado']) {
                if ($dirigido_a) { //SI SE RECHAZA EL DOCUMENTO
                    $reparto = $this->repartoAleatorioParametrizado(
                        $datos_gestor_respuesta['id_proceso_disciplinario'],
                        $datos_gestor_respuesta['id_etapa'],
                        $datos_gestor_respuesta['id_fase'],
                        $datos_gestor_respuesta['descripcion'],
                        $id_funcionario,
                        $dirigido_a,
                        'E_GestorRespuesta',
                        false,
                        null
                    );
                } else {

                    //PREGUNTA SI EL ROL QUE TIENE QUE BUSCAR ES JEFE
                    $respuesta_jefe = DB::select(
                        "
                        SELECT
                            r.id,
                            r.name
                        FROM
                        roles r
                        INNER JOIN funcionalidad_rol fr ON r.id = fr.role_id
                        INNER JOIN mas_funcionalidad mf ON fr.funcionalidad_id = mf.id
                        WHERE r.id = " . $datos_gestor_respuesta['reparto']['id_funcionalidad'] . "
                        AND mf.nombre_mostrar = '" . Constants::FUNCIONALIDAD_ROL['jefe'] . "'"
                    );

                    if (count($respuesta_jefe) > 0) {
                        //BUSCAR JEFE DE LA DEPENDENCIA
                        $repository_dependencia_origen = new RepositoryGeneric();
                        $repository_dependencia_origen->setModel(new DependenciaOrigenModel());
                        $resultado_dependencia = $repository_dependencia_origen->find(auth()->user()->id_dependencia);

                        if ($resultado_dependencia->id_usuario_jefe == null) {
                            $error['estado'] = false;
                            $error['error'] = 'NO ES POSIBLE COMPLETAR EL PROCEDIMIENTO, LA DEPENDENCIA ACTUAL NO TIENE USUARIO JEFE ASIGNADO';

                            return json_encode($error);
                        }

                        //BUSCAR USUARIO
                        $repository_usuario = new RepositoryGeneric();
                        $repository_usuario->setModel(new User());
                        $resultado_usuario = $repository_usuario->find($resultado_dependencia->id_usuario_jefe);

                        $reparto = new stdClass;
                        $reparto->nombre_funcionario = $resultado_usuario->name;
                        $reparto->nombre_completo = $resultado_usuario->nombre;
                        $reparto->apellido_completo = $resultado_usuario->apellido;
                        $reparto->id_dependencia_origen = auth()->user()->id_dependencia;
                        $reparto->id_funcionario_asignado = $resultado_usuario->name;
                        $reparto->num_casos = $resultado_usuario->numero_casos;
                        $reparto->estado = true;
                    } else {
                        $reparto = $this->repartoAleatorioParametrizado(
                            $datos_gestor_respuesta['id_proceso_disciplinario'],
                            $datos_gestor_respuesta['id_etapa'],
                            $datos_gestor_respuesta['id_fase'],
                            $datos_gestor_respuesta['descripcion'],
                            $datos_gestor_respuesta['reparto']['id_funcionalidad'],
                            $datos_gestor_respuesta['reparto']['id_funcionario_asignado'],
                            'E_GestorRespuesta',
                            true,
                            null
                        );
                    }
                }
            }
            //REPARTO ALEATORIO
            if ($reparto && $reparto->estado == false) {
                return $reparto;
            }

            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datos_gestor_respuesta['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

            // LOG PROCESO DISCIPLINARIO
            $logRequest['id_proceso_disciplinario'] = $datos_gestor_respuesta['id_proceso_disciplinario'];
            $logRequest['id_etapa'] =  LogTrait::etapaActual($datos_gestor_respuesta['id_proceso_disciplinario']);
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['descripcion'] = substr($datos_gestor_respuesta['descripcion'], 0, 4000);
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['id_fase'] = $datos_gestor_respuesta['id_fase'];
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_funcionario_actual'] = $reparto->nombre_funcionario;
            $logRequest['id_funcionario_asignado'] = $reparto->nombre_funcionario;
            $logModel = new LogProcesoDisciplinarioModel();

            // ACTUALIZAR EL LOG CON EL ESTADO REMITIDO
            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $logRequest['id_proceso_disciplinario'])
                ->update(['id_tipo_log' => Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['contestado']]);

            // INSERTAR ULTIMO PROCESO
            $info_log = LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            $reparto->nombre_completo = $reparto->nombre_completo . " " . $reparto->apellido_completo;
            $this->numeroCasosUsuario($reparto->nombre_funcionario);


            $respuesta = $this->repository->create($datos_gestor_respuesta);

            if ($ultimo_usuario) {

                set_time_limit(120);

                //SE OBTIENE TODOS LOS DOCUMENTOS QUE VAN A SER SUBIDOS A SIRIUS
                $documento = DocumentoSiriusModel::where('uuid', $datos_gestor_respuesta['id_documento_sirius'])->get();
                $documentos = DocumentoSiriusModel::where('id_log_proceso_disciplinario', $documento[0]['id_log_proceso_disciplinario'])->orderByDesc('created_at')->get();

                foreach($documentos as $documento){
                    $query_gestor_respuesta[0]->id_documento_sirius = $documento->uuid;
                    $estado_documento = $this->subirDocumentoAprobado($query_gestor_respuesta[0]);
    
                    if ($estado_documento && $estado_documento->estado == false) {
                        return $estado_documento;
                    }
                }

            }

            $respuesta =  GestorRespuestaResource::make($respuesta)->rolSeleccionado($reparto);
            $array = json_decode(json_encode($respuesta));            
            
            // ACTUALIZAR ID_FASE_PROCESO EN EL LOG
            LogProcesoDisciplinarioModel::where('uuid', $info_log->uuid)
            ->update(['id_fase_registro' => $array->id]);
            //dd("Para");
            DB::connection()->commit();
            return $respuesta;
        } catch (\Exception $e) {
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
    public function storeWithDocumento(GestorRespuestaDocumentoFormRequest $request)
    {
        try {
            DB::connection()->beginTransaction();

            //Se establecen variables de incio
            $datos_gestor_respuesta = $request->validated()["data"]["attributes"];
            $dirigido_a = null;

            foreach($datos_gestor_respuesta as &$datos_gestor){
                $datos_gestor['proceso_finalizado'] = false;
                $datos_gestor['orden_funcionario'] = 0;
                $datos_gestor['id_etapa'] = Constants::ETAPA['evaluacion'];
                $datos_gestor['id_fase'] = Constants::FASE['gestor_respuesta'];
                $datos_gestor['eliminado'] = false;
            }

            // Es importante eliminar la referencia al final del bucle para evitar comportamientos inesperados en otras partes del código.
            unset($datos_gestor);

            //dd($datos_gestor_respuesta[0]);

            $reparto = null;
            $unico_rol = null;

            $query_max_version = $this->repository->customQuery(
                function ($model) use ($datos_gestor_respuesta) {
                    return $model
                        ->where('id_proceso_disciplinario', $datos_gestor_respuesta[0]['id_proceso_disciplinario'])
                        ->where('eliminado', false)
                        ->max('version');
                }
            );

            $query_gestor_respuesta = $this->repository->customQuery(
                function ($model) use ($datos_gestor_respuesta, $query_max_version) {
                    return $model
                        ->where('id_proceso_disciplinario', $datos_gestor_respuesta[0]['id_proceso_disciplinario'])
                        ->where('version', $query_max_version)
                        ->where('eliminado', false)
                        ->orderby('created_at', 'desc')
                        ->get();
                }
            );

            // Consultar ultima evaluacion
            $evaluacion_activa = DB::select(
                "
                    SELECT
                        resultado_evaluacion
                    FROM
                    evaluacion
                    WHERE id_proceso_disciplinario = '" . $datos_gestor_respuesta[0]['id_proceso_disciplinario'] . "'
                    AND eliminado = 0
                    ORDER BY created_at DESC
                "
            );

            if (count($evaluacion_activa) > 0) {
                foreach($datos_gestor_respuesta as &$datos_gestor){
                    $datos_gestor['id_tipo_evaluacion'] = $evaluacion_activa[0]->resultado_evaluacion;
                }
            } else {
                foreach($datos_gestor_respuesta as &$datos_gestor){
                    $datos_gestor['id_tipo_evaluacion'] = Constants::RESULTADO_EVALUACION['sin_evaluacion'];
                }
            }
            
            // Es importante eliminar la referencia al final del bucle para evitar comportamientos inesperados en otras partes del código.
            unset($datos_gestor);

            //Se enlista modelo del Orden de los Funcionarios
            $repository_compulsa = new RepositoryGeneric();
            $repository_compulsa->setModel(new OrdenFuncionarioModel());

            if ($query_max_version) {
                //Obtiene la lista completa de los tipos de funcionarios, dada la configuración anterior
                $respuesta_funcionarios = $repository_compulsa->customQuery(
                    function ($model) use ($query_gestor_respuesta, $datos_gestor_respuesta) {
                        return $model
                            ->where('grupo', $query_gestor_respuesta[0]->id_mas_orden_funcionario)
                            ->where('id_evaluacion', $datos_gestor_respuesta[0]['id_tipo_evaluacion']) //TIPO DE EVALUACION
                            ->orderby('orden', 'asc')
                            ->get();
                    }
                )->all();

                $unico_rol = $respuesta_funcionarios[0]->unico_rol;
            } else { //Se obtiene el ulitmo registro de la configuración vigente, para obtener la configuración a asignar, en caso de que no exista un registro en Gestor Respuesta

                //TEMPORAL SABER QUE TIPO DE EXPEDIENTE ES -> ACTUALMENTE LA CONFIGURACION SERA APLICADA A INCORPORACION - QUEJA
                $resultado_expediente = $this->obtenerDatosProcesoDisciplinario($datos_gestor_respuesta[0]['id_proceso_disciplinario']);
                //TEMPORAL
                $respuesta_ultima_configuracion = $repository_compulsa->customQuery(
                    function ($model) use ($datos_gestor_respuesta, $resultado_expediente) {
                        return $model
                            ->where('id_evaluacion', $datos_gestor_respuesta[0]['id_tipo_evaluacion'])
                            ->where('id_expediente', $resultado_expediente->id_tipo_expediente)
                            ->where(function ($model) use ($resultado_expediente) {
                                $model->where('id_sub_expediente', $resultado_expediente->sub_tipo_expediente_id)
                                    ->orWhereNull('id_sub_expediente');
                            })
                            ->where(function ($model) use ($resultado_expediente) {
                                $model->where('id_tercer_expediente', $resultado_expediente->id_tercer_expediente)
                                    ->orWhereNull('id_tercer_expediente');
                            })
                            ->orderby('created_at', 'desc')
                            ->get();
                    }
                );

                //Obtiene la lista completa de los tipos de funcionarios, dada la configuración anterior
                if ($respuesta_ultima_configuracion->count() > 0) {
                    $respuesta_ultima_configuracion = $respuesta_ultima_configuracion->first();
                    $respuesta_funcionarios = $repository_compulsa->customQuery(
                        function ($model) use ($respuesta_ultima_configuracion) {
                            return $model
                                ->where('grupo', $respuesta_ultima_configuracion->grupo)
                                ->orderby('orden', 'asc')
                                ->get();
                        }
                    )->all();
                    $unico_rol = $respuesta_ultima_configuracion->unico_rol;
                } else {

                    $evaluacion = ResultadoEvaluacionModel::where('id', $datos_gestor_respuesta[0]['id_tipo_evaluacion'])->get();

                    //throw new NotFoundHttpException('Lista de parametrización de usuarios no encontrada, si el error persiste comuníquese con el administrador');
                    $error = new stdClass;
                    $error->estado = false;
                    $error->error = 'LISTA DE PARAMETRIZACIÓN DE USUARIOS PARA ' . $evaluacion[0]->nombre . ' NO ENCONTRADA, SI EL ERROR PERSISTE COMUNÍQUESE CON EL ADMINISTRADOR.';
                    return $error;
                }
            }

            //Se valida que exista un proceso iniciado
            if ($query_max_version) {
                foreach($datos_gestor_respuesta as &$datos_gestor){
                    $datos_gestor['orden_funcionario'] = 0;
                    $datos_gestor['version'] = $query_max_version + 1;
                    $datos_gestor['nuevo_documento'] = true;
                    $datos_gestor['id_mas_orden_funcionario'] = $respuesta_funcionarios[0]['grupo'];
                    $datos_gestor['created_user'] = auth()->user()->name;
                }
            } else { //Si no existe el proceso, comienza uno nuevo
                foreach($datos_gestor_respuesta as &$datos_gestor){
                    $datos_gestor['id_mas_orden_funcionario'] = $respuesta_funcionarios[0]->grupo;
                    $datos_gestor['orden'] = 2;
                    $datos_gestor['nuevo_documento'] = true;
                    $datos_gestor['created_user'] = auth()->user()->name;
                }
            }

            // Es importante eliminar la referencia al final del bucle para evitar comportamientos inesperados en otras partes del código.
            unset($datos_gestor);


            $mismo_usuario_recibe = false;

            //REPARTO ALEATORIO
            if ($unico_rol) {
                foreach($datos_gestor_respuesta as &$datos_gestor){
                    $datos_gestor['proceso_finalizado'] = true;
                }

                // Es importante eliminar la referencia al final del bucle para evitar comportamientos inesperados en otras partes del código.
                unset($datos_gestor);
                
                if ($respuesta_ultima_configuracion->id_funcionario == 0) {
                    $reparto = new stdClass;
                    $reparto->nombre_funcionario = auth()->user()->name;
                    $reparto->nombre_completo = auth()->user()->nombre;
                    $reparto->apellido_completo = auth()->user()->apellido;
                    $reparto->id_dependencia_origen = auth()->user()->id_dependencia;
                    $reparto->id_funcionario_asignado = auth()->user()->name;
                    $reparto->estado = true;
                    $mismo_usuario_recibe = true;
                } else {
                    $mismo_usuario_recibe = false;
                }
            }

            if ($unico_rol || $mismo_usuario_recibe) {
                $id_funcionario = $respuesta_funcionarios[0]['id_funcionario'];
            } else {
                $id_funcionario = $respuesta_funcionarios[1]['id_funcionario']; //Funcionario que recibe el proceso
            }

            if (!$mismo_usuario_recibe) {

                $respuesta_jefe = DB::select(
                    "
                    SELECT
                        r.id,
                        r.name
                    FROM
                    roles r
                    INNER JOIN funcionalidad_rol fr ON r.id = fr.role_id
                    INNER JOIN mas_funcionalidad mf ON fr.funcionalidad_id = mf.id
                    WHERE r.id = " . $id_funcionario . "
                    AND mf.nombre_mostrar = '" . Constants::FUNCIONALIDAD_ROL['jefe'] . "'"
                );

                if (count($respuesta_jefe) > 0) { //AQUI PREGUNTARA SI ES JEFE
                    //BUSCAR JEFE DE LA DEPENDENCIA
                    $repository_dependencia_origen = new RepositoryGeneric();
                    $repository_dependencia_origen->setModel(new DependenciaOrigenModel());
                    $resultado_dependencia = $repository_dependencia_origen->find(auth()->user()->id_dependencia);

                    if ($resultado_dependencia->id_usuario_jefe == null) {
                        $error['estado'] = false;
                        $error['error'] = 'NO ES POSIBLE COMPLETAR EL PROCEDIMIENTO, LA DEPENDENCIA ACTUAL NO TIENE USUARIO JEFE ASIGNADO';

                        return json_encode($error);
                    }

                    //BUSCAR USUARIO
                    $repository_usuario = new RepositoryGeneric();
                    $repository_usuario->setModel(new User());
                    $resultado_usuario = $repository_usuario->find($resultado_dependencia->id_usuario_jefe);

                    $reparto = new stdClass;
                    $reparto->nombre_funcionario = $resultado_usuario->name;
                    $reparto->nombre_completo = $resultado_usuario->nombre;
                    $reparto->apellido_completo = $resultado_usuario->apellido;
                    $reparto->id_dependencia_origen = auth()->user()->id_dependencia;
                    $reparto->id_funcionario_asignado = $resultado_usuario->name;
                    $reparto->num_casos = $resultado_usuario->numero_casos;
                    $reparto->estado = true;
                } else {
                    $reparto = $this->repartoAleatorioParametrizado(
                        $datos_gestor_respuesta[0]['id_proceso_disciplinario'],
                        $id_funcionario,
                        $datos_gestor_respuesta[0]['id_etapa'],
                        $datos_gestor_respuesta[0]['descripcion'],
                        $id_funcionario,
                        null,
                        'E_GestorRespuesta',
                        true,
                        null
                    );
                }
            }

            //REPARTO ALEATORIO
            if ($reparto && $reparto->estado == false) {
                return $reparto;
            }

            //Subir documento
            $respuesta = null;
            $id_log_proceso_disciplinario = null;
            $primer_registro = true;
            foreach($datos_gestor_respuesta as &$datos_gestor){
                $respuesta_subida = $this->subirDocumento($datos_gestor, $id_log_proceso_disciplinario);

                if ($respuesta_subida && !$respuesta_subida->estado) {
                    $error = new stdClass;
                    $error->estado = false;
                    $error->error = $respuesta_subida->error ? $respuesta_subida->error : 'NO SE PUEDE SUBIR EL DOCUMENTO, SI EL ERROR PERSISTE COMUNÍQUESE CON EL ADMINISTRADOR';
                    return $error;
                }

                $datos_gestor['id_documento_sirius'] = $respuesta_subida->uuid;
                $id_log_proceso_disciplinario = $respuesta_subida->id_log_proceso_disciplinario;
                if($primer_registro){
                    $respuesta = $this->repository->create($datos_gestor);
                    $primer_registro = false;
                }
            }

            // Es importante eliminar la referencia al final del bucle para evitar comportamientos inesperados en otras partes del código.
            unset($datos_gestor);

            $datos_rol_siguiente = null;
            $datos_rol_siguiente['nombre_funcionario'] = $reparto->nombre_funcionario;
            $datos_rol_siguiente['nombre_completo'] = $reparto->nombre_completo . ' ' . $reparto->apellido_completo;
            $datos_rol_siguiente['id_dependencia_origen'] = $reparto->id_dependencia_origen;
            $datos_rol_siguiente['id_funcionario_asignado'] = $reparto->id_funcionario_asignado;

            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datos_gestor_respuesta[0]['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

            // Guardar en el LOG
            $logRequest['id_proceso_disciplinario'] = $datos_gestor_respuesta[0]['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = $datos_gestor_respuesta[0]['id_etapa'];
            $logRequest['id_fase'] =  $datos_gestor_respuesta[0]['id_fase'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa'];;
            $logRequest['descripcion'] = substr($datos_gestor_respuesta[0]['descripcion'], 0, 4000);
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido']; // Remisionado
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['id_funcionario_actual'] = $datos_rol_siguiente['id_funcionario_asignado'];
            $logRequest['id_funcionario_asignado'] = $datos_rol_siguiente['id_funcionario_asignado'];
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_fase_registro'] =  $respuesta->uuid;
            $logModel = new LogProcesoDisciplinarioModel();
            $respuesta_log = LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            $this->numeroCasosUsuario($reparto->nombre_funcionario);

            if ($datos_gestor_respuesta[0]['proceso_finalizado'] == true && $unico_rol) {

                $query_gestor_respuesta = $this->repository->customQuery(
                    function ($model) use ($datos_gestor_respuesta) {
                        return $model
                            ->where('id_proceso_disciplinario', $datos_gestor_respuesta[0]['id_proceso_disciplinario'])
                            ->where('eliminado', false)
                            ->where('version', 1)
                            ->orderby('created_at', 'desc')
                            ->get();
                    }
                );

                //SE OBTIENE TODOS LOS DOCUMENTOS QUE VAN A SER SUBIDOS A SIRIUS
                $documento = DocumentoSiriusModel::where('uuid', $datos_gestor_respuesta[0]['id_documento_sirius'])->get();
                $documentos = DocumentoSiriusModel::where('id_log_proceso_disciplinario', $documento[0]['id_log_proceso_disciplinario'])->orderByDesc('created_at')->get();

                foreach($documentos as $documento){
                    $query_gestor_respuesta[0]->id_documento_sirius = $documento->uuid;
                    $estado_documento = $this->subirDocumentoAprobado($query_gestor_respuesta[0]);
                    if ($estado_documento && $estado_documento->estado == false) {
                        return $estado_documento;
                    }
                }

            }

            //dd("Error 2");

            DB::connection()->commit();

            return GestorRespuestaResource::make($respuesta)->rolSiguiente((object) $datos_rol_siguiente);
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

    public function subirDocumento($datosRequest, $id_log_proceso_disciplinario = null)
    {
        try {

            if (!env('SUBIR_DOCUMENTACION_SIRIUS') && !env('SUBIR_DOCUMENTACION_LOCAL')) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "ERROR EN LA CONFIGURACIÓN DEL ENV POR FAVOR COMUNÍQUESE CON EL ADMINISTRADOR";
                return $error;
            }

            $repository_tbint_documento_sirius_descripcion = new RepositoryGeneric();
            $repository_tbint_documento_sirius_descripcion->setModel(new TbintDocumentoSiriusDescripcionModel());

            $uuid_descripcion = null;
            $descripcion = null;
            $uuid_descripcion_compulsa = null;
            $descripcion_compulsa = null;
            $path = null;

            if($id_log_proceso_disciplinario){
                $datosRequest['id_log_proceso_disciplinario'] = $id_log_proceso_disciplinario;
            }
            else{
                $log_uuid = DB::select(
                    "
                        select
                            uuid
                        from 
                            log_proceso_disciplinario
                        where id_proceso_disciplinario = '" . $datosRequest['id_proceso_disciplinario'] . "'
                        and id_funcionario_actual IS NOT NULL
                    "
                );
    
                $datosRequest['id_log_proceso_disciplinario'] = $log_uuid[0]->uuid;
                $id_log_proceso_disciplinario = $log_uuid[0]->uuid;
            }

            $descripcion = $datosRequest['descripcion'];

            if ($descripcion && $descripcion != null) {
                $datosRequest['created_user'] = auth()->user()->name;
                $datosRequest['descripcion'] = $descripcion;
                $datosRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
                $result_tbint_documento_sirius_descripcion = $repository_tbint_documento_sirius_descripcion->create($datosRequest);
                $uuid_descripcion = $result_tbint_documento_sirius_descripcion->uuid;
            }

            $datosRequest['created_user'] = auth()->user()->name;
            $datosRequest['es_compulsa'] = false;
            $datosRequest['grupo'] = $uuid_descripcion;
            $path = storage_path() . '/files/gestor_respuesta';
            //Se finaliza modificacion

            $datosRequest['path'] = $path;

            $repository_documento_sirius = new RepositoryGeneric();
            $repository_documento_sirius->setModel(new DocumentoSiriusModel());

            $result_insert_documento_sirus = $repository_documento_sirius->create($datosRequest);

            if (!env('OMITIR_SUBIDA_ARCHIVO')) {
                /*Guardar File*/
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $path = $path . '/' . $result_insert_documento_sirus->uuid . '_' . $datosRequest['nombre_archivo'];

                $documentos[0]['path'] = $path;
                $documentos[0]['nombre'] = $result_insert_documento_sirus->uuid . '_' . $datosRequest['nombre_archivo'];
                $documentos[0]['id_documento'] = $result_insert_documento_sirus->uuid;

                $b64 = $datosRequest['file64'];
                $bin = base64_decode($b64, true);
                file_put_contents($path, $bin);
                /*Guardar File*/
                DocumentoSiriusModel::where('uuid', $result_insert_documento_sirus->uuid)->update(['nombre_archivo' => $documentos[0]['nombre']]);
            }

            $log_descripcion = $descripcion;

            if ($descripcion_compulsa != null) {
                $log_descripcion = $descripcion_compulsa;
            }

            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

            $result = new stdClass;
            $result->estado = true;
            $result->uuid = $result_insert_documento_sirus->uuid;
            $result->id_log_proceso_disciplinario = $id_log_proceso_disciplinario;

            return $result;
        } catch (\Exception $e) {
            error_log($e);
            if ((strpos($e->getMessage(), 'Network') !== false) || (strpos($e->getMessage(), 'Request Entity Too Large') !== false)) {
                $result = new stdClass;
                $result->estado = false;
                return $result;
            }

            $result = new stdClass;
            $result->estado = false;
            return $result;
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

    public function getGestorRespuestaByProcesoDisciplinario($id_proceso_disciplinario)
    {
        try {

            $rol_previo = null;

            $respuesta_gestor_respuesta = $this->repository->customQuery(
                function ($model) use ($id_proceso_disciplinario) {
                    return $model
                        ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
                        ->where('eliminado', false)
                        ->orderby('created_at', 'desc')
                        ->get();
                }
            );

            if ($respuesta_gestor_respuesta->count() <= 0) {
                return GestorRespuestaCollection::make($respuesta_gestor_respuesta);
            } else if ($respuesta_gestor_respuesta[0]->proceso_finalizado) {

                $respuesta_evaluacion = DB::select("
                    SELECT
                        e.created_at,
                        mre.nombre
                    FROM
                    evaluacion e
                    INNER JOIN mas_resultado_evaluacion mre ON mre.id = e.resultado_evaluacion
                    WHERE e.id_proceso_disciplinario = '$id_proceso_disciplinario'
                    AND e.eliminado = 0
                    ORDER BY e.created_at ASC
                ");

                return GestorRespuestaCollection::make($respuesta_gestor_respuesta)->evaluacion($respuesta_evaluacion)->tipoExpediente($this->obtenerDatosProcesoDisciplinario($id_proceso_disciplinario));
            }

            //Se enlista modelo del Orden de los Funcionarios
            $repository_compulsa = new RepositoryGeneric();
            $repository_compulsa->setModel(new OrdenFuncionarioModel());

            $respuesta_gestor_respuesta_version = $this->repository->customQuery(
                function ($model) use ($id_proceso_disciplinario, $respuesta_gestor_respuesta) {
                    return $model
                        ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
                        ->where('version', $respuesta_gestor_respuesta[0]->version)
                        ->orderby('created_at', 'desc')
                        ->get();
                }
            );

            //Obtiene la lista completa de los tipos de funcionarios, dada la configuración anterior

            //construccion query
            $respuesta_funcionarios = $results = DB::select("
                SELECT
                    mof.id,
                    mof.id_funcionario,
                    mof.orden,
                    mof.grupo,
                    mof.estado,
                    mof.funcionario_siguiente,
                    r.name AS nombre_mostrar
                FROM mas_orden_funcionario mof
                INNER JOIN roles r ON mof.id_funcionario = r.id
                WHERE mof.grupo = " . $respuesta_gestor_respuesta[0]['id_mas_orden_funcionario'] . "
                ORDER BY mof.orden ASC
            ");

            //Busqueda del ROL siguiente
            $datos_rol_siguiente = null;
            $id_funcionarios = 0; //DE FORMA TEMPORAL SE SUBE ESTA VARIABLE YA QUE NO SE TIENE DEFINIDO EL ULTIMO USUARIO

            if ($respuesta_gestor_respuesta_version[0]['orden_funcionario'] == 0) {
                $id_funcionarios = 1; //DE FORMA TEMPORAL SE SUBE ESTA VARIABLE YA QUE NO SE TIENE DEFINIDO EL ULTIMO USUARIO
                if (count($respuesta_funcionarios) > 1) {
                    $datos_rol_siguiente['id_funcionario'] = $respuesta_funcionarios[2]->id_funcionario; //El numero establecido es para saltar el primer rol que es el de subida de documentacion y el rol actual que va a aprobar el expediente
                    $datos_rol_siguiente['funcionario'] = $respuesta_funcionarios[2]->nombre_mostrar; //El numero establecido es para saltar el primer rol que es el de subida de documentacion y el rol actual que va a aprobar el expediente
                    //dd($respuesta_funcionarios, $datos_rol_siguiente);
                } else {
                    $datos_rol_siguiente['id_funcionario'] = null;
                    $datos_rol_siguiente['funcionario'] = null;
                }
            } else {
                for ($cont = 0; $cont < count($respuesta_funcionarios); $cont++) {
                    if ($respuesta_funcionarios[$cont]->orden == $respuesta_gestor_respuesta_version[0]['orden_funcionario']) {
                        if (($cont + 2) < count($respuesta_funcionarios)) {
                            $id_funcionarios = $respuesta_funcionarios[$cont + 2]->funcionario_siguiente;
                        } else {
                            $id_funcionarios = 0;
                        }
                    }
                }

                if ($id_funcionarios != 0) {
                    for ($cont = 0; $cont < count($respuesta_funcionarios); $cont++) {
                        if ($respuesta_funcionarios[$cont]->id == $id_funcionarios) {
                            $datos_rol_siguiente['id_funcionario'] = $respuesta_funcionarios[$cont]->id_funcionario;
                            $datos_rol_siguiente['funcionario'] = $respuesta_funcionarios[$cont]->nombre_mostrar;
                        }
                    }
                }
            }

            if ($datos_rol_siguiente && !$respuesta_gestor_respuesta[0]->proceso_finalizado) {

                //PREGUNTA SI EL ROL QUE TIENE QUE BUSCAR ES JEFE
                $respuesta_jefe = DB::select(
                    "
                    SELECT
                        r.id,
                        r.name
                    FROM
                    roles r
                    INNER JOIN funcionalidad_rol fr ON r.id = fr.role_id
                    INNER JOIN mas_funcionalidad mf ON fr.funcionalidad_id = mf.id
                    WHERE r.id = " . $datos_rol_siguiente['id_funcionario'] . "
                    AND mf.nombre_mostrar = '" . Constants::FUNCIONALIDAD_ROL['jefe'] . "'"
                );

                //dd($respuesta_jefe);

                if (count($respuesta_jefe) > 0) {
                    //BUSCAR JEFE DE LA DEPENDENCIA
                    $repository_dependencia_origen = new RepositoryGeneric();
                    $repository_dependencia_origen->setModel(new DependenciaOrigenModel());
                    $resultado_dependencia = $repository_dependencia_origen->find(auth()->user()->id_dependencia);

                    if ($resultado_dependencia->id_usuario_jefe == null) {
                        $error['estado'] = false;
                        $error['error'] = 'NO ES POSIBLE COMPLETAR EL PROCEDIMIENTO, LA DEPENDENCIA ACTUAL NO TIENE USUARIO JEFE ASIGNADO';

                        return json_encode($error);
                    }

                    //BUSCAR USUARIO
                    $repository_usuario = new RepositoryGeneric();
                    $repository_usuario->setModel(new User());
                    $resultado_usuario = $repository_usuario->find($resultado_dependencia->id_usuario_jefe);

                    $datos_rol_siguiente['nombre_funcionario'] = $resultado_usuario->name;
                    $datos_rol_siguiente['nombre_completo'] = $resultado_usuario->nombre . ' ' . $resultado_usuario->apellido;
                    $datos_rol_siguiente['id_dependencia_origen'] = auth()->user()->id_dependencia;
                    $datos_rol_siguiente['id_funcionario_asignado'] = $resultado_usuario->name;
                    $datos_rol_siguiente['estado'] = true;
                } else {
                    $rol_siguiente = $this->repartoAleatorioParametrizado(
                        $id_proceso_disciplinario,
                        null,
                        null,
                        null,
                        $datos_rol_siguiente['id_funcionario'],
                        null,
                        'E_GestorRespuesta',
                        true,
                        null
                    );

                    if ($rol_siguiente->estado) {
                        $datos_rol_siguiente['nombre_funcionario'] = $rol_siguiente->nombre_funcionario;
                        $datos_rol_siguiente['nombre_completo'] = $rol_siguiente->nombre_completo . ' ' . $rol_siguiente->apellido_completo;
                        $datos_rol_siguiente['id_dependencia_origen'] = $rol_siguiente->id_dependencia_origen;
                        $datos_rol_siguiente['id_funcionario_asignado'] = $rol_siguiente->id_funcionario_asignado;
                        $datos_rol_siguiente['estado'] = $rol_siguiente->estado;
                    } else {
                        if ($id_funcionarios > 0) { //AGREGA ESTA CONFIGURACION TEMPORAL
                            $datos_rol_siguiente = $rol_siguiente;
                            $datos_rol_siguiente->ultimo_usuario = false;
                        } else {
                            $datos_rol_siguiente = $rol_siguiente;
                            $datos_rol_siguiente->ultimo_usuario = true;
                        }
                    }
                }
            } else { //AGREGA ESTA CONFIGURACION TEMPORAL
                $datos_rol_siguiente = new stdClass;
                $datos_rol_siguiente->ultimo_usuario = true;
            }

            //Se enlista modelo de los usuarios
            $repository_user = new RepositoryGeneric();
            $repository_user->setModel(new User());
            $name_user = $respuesta_gestor_respuesta_version[count($respuesta_gestor_respuesta_version) - 1]->created_user;

            $respuesta_usuario = $repository_user->customQuery(
                function ($model) use ($name_user) {
                    return $model
                        ->where('name', $name_user)
                        ->get();
                }
            )->all();

            $resultado_expediente = $this->obtenerDatosProcesoDisciplinario($id_proceso_disciplinario);

            $datos_rol_anterior = $this->repartoAleatorioParametrizado(
                $id_proceso_disciplinario,
                0,
                0,
                null,
                $respuesta_funcionarios[0]->id_funcionario,
                $respuesta_usuario[0]->name,
                'E_GestorRespuesta',
                false,
                null
            );

            if (
                $resultado_expediente->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela'] &&
                (isset($datos_rol_anterior->estado) && !$datos_rol_anterior->estado ||
                    $respuesta_usuario[0]->name != $datos_rol_anterior->nombre_funcionario
                )
            ) { //SE DEBEN TRAER TODOS LOS ROLES INICIALES EN CASO DE SER UNA TUTELA

                $datos_rol_anterior = DB::select("
                    SELECT
                        u.id AS id_funcionario,
                        u.name AS nombre_funcionario,
                        u.name AS id_funcionario_actual,
                        u.name AS id_funcionario_asignado,
                        u.nombre AS nombre_completo,
                        u.apellido AS apellido_completo,
                        u.id_dependencia AS id_dependencia_origen,
                        u.email AS email,
                        r.name,
                        u.reparto_habilitado,
                        u.estado AS estado
                    FROM
                        users u
                    INNER JOIN users_roles ur ON u.id = ur.user_id
                    INNER JOIN roles r ON ur.role_id = r.id
                    INNER JOIN users_tipo_expediente ute ON u.id = ute.user_id
                    INNER JOIN mas_tipo_expediente mte ON ute.tipo_expediente_id = mte.id
                    WHERE r.id = " . $respuesta_funcionarios[0]->id_funcionario . "
                    AND u.estado = " . Constants::ESTADOS['activo'] . "
                    AND u.reparto_habilitado = " . Constants::ESTADOS['activo'] . "
                    AND u.id_dependencia = " . auth()->user()->id_dependencia . "
                    AND ute.tipo_expediente_id = " . $resultado_expediente->id_tipo_expediente . "
                    AND ute.sub_tipo_expediente_id = " . $resultado_expediente->sub_tipo_expediente_id . "
                    AND ur.role_id = (
                        --VERIFICA QUE EL ROL PERTENESCA A LA FASE Y TENGA PERMISO DE CREAR
                        SELECT
                            r2.id
                        FROM
                        roles r2
                        INNER JOIN users_roles ur2 ON r2.id = ur2.role_id
                        INNER JOIN funcionalidad_rol fr2 ON fr2.role_id = ur2.role_id
                        INNER JOIN mas_funcionalidad mf2 ON fr2.funcionalidad_id = mf2.id
                        INNER JOIN mas_modulo mm2 ON mf2.id_modulo = mm2.id
                        WHERE r2.id = ur.role_id
                        AND mm2.nombre = 'E_GestorRespuesta'
                        AND mf2.nombre = 'Crear'
                        GROUP BY r2.id
                    )
                    GROUP BY u.id, u.name, u.nombre, u.apellido, u.id_dependencia, u.email, r.name, u.reparto_habilitado, u.estado
                ");

                if (count($datos_rol_anterior) <= 0) {
                    $error = new stdClass;
                    $error->estado = false;
                    $error->error = 'NO EXISTEN FUNCIONARIOS CON PERMISOS PARA GESTIONAR ESTE TIPO DE EXPEDIENTE.';
                    $datos_rol_anterior = $error;
                }

                $datos_rol_previo = DB::select("
                    SELECT
                        u.name,
                        u.nombre,
                        u.apellido,
                        u.estado,
                        u.reparto_habilitado,
                        ur.role_id,
                        mdo.nombre AS dependencia,
                        u.id_dependencia AS id_dependencia_origen,
                        ute.tipo_expediente_id,
                        ute.sub_tipo_expediente_id
                    FROM
                        users u
                    LEFT OUTER JOIN  users_roles ur ON u.id = ur.user_id
                    LEFT OUTER JOIN  mas_dependencia_origen mdo ON u.id_dependencia = mdo.id
                    LEFT OUTER JOIN  users_tipo_expediente ute ON u.id = ute.user_id
                    WHERE u.name = '" . $respuesta_usuario[0]->name . "'
                    AND ur.role_id = " . $respuesta_funcionarios[0]->id_funcionario . "
                    AND ute.tipo_expediente_id = " . $resultado_expediente->id_tipo_expediente . "
                    AND ute.sub_tipo_expediente_id = " . $resultado_expediente->sub_tipo_expediente_id . "
                    AND ur.role_id = (
                        --VERIFICA QUE EL ROL PERTENESCA A LA FASE Y TENGA PERMISO DE CREAR
                        SELECT
                            r2.id
                        FROM
                        roles r2
                        INNER JOIN users_roles ur2 ON r2.id = ur2.role_id
                        INNER JOIN funcionalidad_rol fr2 ON fr2.role_id = ur2.role_id
                        INNER JOIN mas_funcionalidad mf2 ON fr2.funcionalidad_id = mf2.id
                        INNER JOIN mas_modulo mm2 ON mf2.id_modulo = mm2.id
                        WHERE r2.id = ur.role_id
                        AND mm2.nombre = 'E_GestorRespuesta'
                        AND mf2.nombre = 'Crear'
                        GROUP BY r2.id
                    )
                ");

                //dd($datos_rol_previo);

                if (count($datos_rol_previo) <= 0) {

                    $users = DB::select("
                        SELECT
                            u.name,
                            u.nombre,
                            u.apellido,
                            u.estado,
                            u.reparto_habilitado,
                            u.id_dependencia,
                            mdo.nombre AS dependencia
                        FROM
                            users u
                        LEFT OUTER JOIN  mas_dependencia_origen mdo ON u.id_dependencia = mdo.id
                        WHERE u.name = '" . $respuesta_usuario[0]->name . "'
                    ");

                    $rol_previo = new stdClass;
                    $rol_previo->nombre = $users[0]->nombre . ' ' . $users[0]->apellido . ' (' . $users[0]->dependencia . ')';
                    $rol_previo->lista_errores = '1. EL USUARIO NO CUENTA CON LOS PERMISOS Y/U ROLES PARA CONTINUAR CON EL EXPEDIENTE';

                    if (is_null($users[0]->id_dependencia)) {
                        $rol_previo->lista_errores .= ', ADEMAS NO CUENTA CON UNA DEPENDENCIA';
                    }

                    if ($users[0]->id_dependencia != auth()->user()->id_dependencia) {
                        $rol_previo->lista_errores .= ', ADEMAS PERTENCE A OTRA DEPENDENCIA';
                        //dd($users[0]->dependencia, auth()->user()->id_dependencia);
                    }
                } else {
                    $rol_previo = new stdClass;
                    $rol_previo->nombre = $datos_rol_previo[0]->nombre . ' ' . $datos_rol_previo[0]->apellido . ' (' . $datos_rol_previo[0]->dependencia . ')';
                    $rol_previo->lista_errores = 'EL USUARIO NO CUENTA CON LOS PERMISOS O ROLES PARA CONTINUAR CON EL EXPEDIENTE';

                    if (is_null($datos_rol_previo[0]->id_dependencia_origen)) {
                        $rol_previo->lista_errores .= ', ADEMAS NO CUENTA CON UNA DEPENDENCIA';
                    }

                    if ($datos_rol_previo[0]->id_dependencia_origen != auth()->user()->id_dependencia) {
                        $rol_previo->lista_errores .= ', ADEMAS PERTENCE A OTRA DEPENDENCIA';
                    }
                }
            }

            if (isset($datos_rol_anterior->estado) && $datos_rol_anterior->estado) {
                $datos_rol_anterior->nombre_completo = $datos_rol_anterior->nombre_completo . ' ' . $datos_rol_anterior->apellido_completo;
                $datos_rol_anterior->id_dependencia_origen = $datos_rol_anterior->id_dependencia_origen;
            }

            $respuesta_usuario_actual = DB::select("
                SELECT
                    u.NAME AS nombre_funcionario,
                    u.NOMBRE AS nombre_completo,
                    u.APELLIDO AS apellido_completo,
                    r.name AS nombre_mostrar
                FROM users u
                INNER JOIN users_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE u.NAME = '" . auth()->user()->name . "'
                GROUP BY u.NAME, u.NOMBRE, u.APELLIDO, r.name
            ");

            if (count($respuesta_usuario_actual) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = 'EL USUARIO NO TIENE PERMISO PARA GESTIONAR ESTE TIPO DE EXPEDIENTE';
                return $error;
            }

            $datos_rol_actual = null;

            //dd($respuesta_funcionarios, $respuesta_gestor_respuesta, $respuesta_gestor_respuesta[0]['orden_funcionario']);
            if ($respuesta_gestor_respuesta[0]['id_mas_orden_funcionario'] != 0) {
                for ($cont = 0; $cont < count($respuesta_funcionarios); $cont++) {
                    if ($respuesta_funcionarios[$cont]->orden == $respuesta_gestor_respuesta[0]['orden_funcionario'] + 2) {
                        $datos_rol_actual['roles'] = $respuesta_funcionarios[$cont]->nombre_mostrar;
                        $cont = count($respuesta_usuario_actual) + 1;
                    }
                }
            } else {
                $datos_rol_actual['roles'] = null;
                $datos_rol_actual['nombre_completo'] = null;
            }

            $datos_rol_actual['nombre_completo'] = $respuesta_usuario_actual[0]->nombre_completo . ' ' . $respuesta_usuario_actual[0]->apellido_completo;

            $respuesta_evaluacion = DB::select("
                SELECT
                    e.created_at,
                    mre.nombre
                FROM
                    evaluacion e
                INNER JOIN mas_resultado_evaluacion mre ON mre.id = e.resultado_evaluacion
                WHERE e.id_proceso_disciplinario = '$id_proceso_disciplinario'
                AND e.eliminado = " . Constants::ESTADOS_ELIMINADO['no_eliminado'] . "
                ORDER BY e.created_at ASC
            ");

            return GestorRespuestaCollection::make($respuesta_gestor_respuesta)->rolSiguiente((object) $datos_rol_siguiente)->rolAnterior($datos_rol_anterior)->rolActual($datos_rol_actual)->evaluacion($respuesta_evaluacion)->rolPrevio($rol_previo)->tipoExpediente($this->obtenerDatosProcesoDisciplinario($id_proceso_disciplinario));
        } catch (\Exception $e) {

            error_log($e);

            //dd($e);

            if (empty($results)) {

                $error['estado'] = false;
                $error['error'] = 'NO EXISTEN FUNCIONARIOS CON PERMISOS PARA GESTIONAR ESTE TIPO DE EXPEDIENTE.';

                return json_encode($error);
            }

            return $e;
        }
    }

    private function subirDocumentoAprobado($gestor_respuesta)
    {
        try {

            $interesado = DB::select("
                SELECT
                    i.uuid,
                    i.id_tipo_interesao,
                    i.id_tipo_sujeto_procesal,
                    i.id_proceso_disciplinario,
                    i.tipo_documento,
                    i.numero_documento,
                    i.primer_nombre,
                    i.segundo_nombre,
                    i.primer_apellido,
                    i.segundo_apellido,
                    i.direccion,
                    i.direccion_json,
                    i.email,
                    i.telefono_celular,
                    i.telefono_fijo,
                    i.entidad,
                    i.cargo,
                    i.id_tipo_entidad,
                    i.nombre_entidad,
                    md.nombre AS nombre_departamento,
                    mc.nombre AS nombre_ciudad
                FROM
                    interesado i
                FULL JOIN mas_departamento md ON i.id_departamento = md.id
                FULL JOIN mas_ciudad mc ON i.id_ciudad = mc.id
                WHERE i.id_proceso_disciplinario = '" . $gestor_respuesta->id_proceso_disciplinario . "'
                AND i.estado = " . Constants::ESTADOS['activo'] . "
                ORDER BY i.created_at ASC
            ");

            //Primero se contruye el protocolo de comunicacion SIRIUS - PERSONA NATURAL
            $documetoSirius[0]['nombre_archivo'] = $gestor_respuesta['nombre_archivo'];
            $documetoSirius[0]['es_compulsa'] = $gestor_respuesta['es_compulsa'];
            $documetoSirius[0]['descripcion'] = $gestor_respuesta['descripcion'];
            $documetoSirius[0]['num_folios'] = $gestor_respuesta['num_folios'];
            $documetoSirius[0]['fecha_documento'] = $gestor_respuesta['created_at'];
            $siriusTrackId = $this->generarRadicado($this->generarCuerpoPeticionSirius($documetoSirius, $interesado[0]));

            if ($siriusTrackId && isset($siriusTrackId->estado)) { //Validación de que se haya recibido respuesta de sirius
                if (!$siriusTrackId->estado) {
                    return $siriusTrackId;
                } else {
                    $error = new stdClass;
                    $error->estado = false;
                    $error->error = 'HA OCURRIDO UN ERROR CON SIRIUS, SI EL ERROR PERSISTE COMUNÍQUESE CON EL ADMINISTRADOR';
                    return $error;
                }
            }

            $documento_sirius = DocumentoSiriusModel::where('uuid', $gestor_respuesta->id_documento_sirius)->latest('created_at')->get();

            $path_new = null;
            $path_old = null;
            $nombre_nuevo = null;
            $sirius_habilitado = false;

            if (env('SUBIR_DOCUMENTACION_SIRIUS')) {
                $sirius_habilitado = true;
                $nombre_actualizado = explode('_', $documento_sirius[0]->nombre_archivo);
                $path_new = $documento_sirius[0]->path . '/' . $siriusTrackId['trackId'] . '_1_' . $nombre_actualizado[count($nombre_actualizado)-1];
                $path_old = $documento_sirius[0]->path . '/' . $documento_sirius[0]->nombre_archivo;
                rename($path_old, $path_new);
                $nombre_nuevo = $siriusTrackId['trackId'] . '_1_' . $nombre_actualizado[count($nombre_actualizado)-1];
                //dd($nombre_nuevo);
                DocumentoSiriusModel::where('uuid', $gestor_respuesta->id_documento_sirius)->update(['nombre_archivo' => $nombre_nuevo]);
            } else {
                $path_new = $documento_sirius[0]->path . '/' . $documento_sirius[0]->nombre_archivo;
                $nombre_nuevo = $documento_sirius[0]->nombre_archivo;
            }

            $documentos[0]['path'] = $path_new;
            $documentos[0]['nombre'] = $nombre_nuevo;
            $documentos[0]['id_documento'] = $documento_sirius[0]->uuid;

            $sirius_ecmId = $this->subirDocumentoSirius($documentos, $siriusTrackId['trackId']);
            if ($sirius_ecmId && isset($sirius_ecmId->estado)) { //Validación de que se haya recibido respuesta negativa de sirius
                if (!$sirius_ecmId->estado == false) {
                    /*foreach ($documentos as $datos) {
                        unlink($datos['path']); //Se elimina archivo
                    }*/
                    rename($path_new, $path_old);
                    return $sirius_ecmId;
                } else {
                    rename($path_new, $path_old);
                    $error = new stdClass;
                    $error->estado = false;
                    $error->error = 'HA OCURRIDO UN ERROR CON SIRIUS, SI EL ERROR PERSISTE COMUNÍQUESE CON EL ADMINISTRADOR';
                    return $error;
                }
            }

            $datosUpdate = null;

            foreach ($documentos as $datos) {
                $datosUpdate['sirius_ecm_id'] = $sirius_ecmId['ecmId'];
                $datosUpdate['sirius_track_id'] = $siriusTrackId['trackId'];
                DocumentoSiriusModel::where('uuid', $gestor_respuesta->id_documento_sirius)->update($datosUpdate, $datos['id_documento']);
            }
        } catch (\Exception $e) {

            // error_log("Sirius -> " . $sirius_habilitado);
            // error_log("path_new -> " . $path_new);
            // error_log("path_old -> " . $path_old);
            
            if ($sirius_habilitado) {
                // error_log("Entro");
                if ($path_new != $path_old) {
                    if (file_exists($path_new)) {
                        rename($path_new, $path_old);
                    }
                }
            }
            
            error_log($e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = 'NO ES POSIBLE SUBIR EL DOCUMENTO AL SISTEMA DE GESTIÓN DOCUMENTAL SIRIUS, SI EL ERROR PERSISTE COMUNÍQUESE CON EL ADMINISTRADOR';
            return $error;
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
