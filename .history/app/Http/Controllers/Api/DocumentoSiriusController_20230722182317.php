<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentoSiriusFormRequest;
use App\Http\Resources\DocumentoSirius\DocumentoSiriusCollection;
use App\Http\Resources\DocumentoSirius\DocumentoSiriusResource;
use App\Models\DocumentoSiriusModel;
use App\Models\TbintDocumentoSiriusDescripcionModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Controllers\Traits\MailTrait;
use App\Http\Controllers\Traits\ProcesoDisciplinarioTrait;
use App\Http\Controllers\Traits\SiriusTrait;
use App\Http\Requests\DocumentoSiriusDescargaFormRequest;
use App\Http\Requests\DocumentoSiriusUpdateFormRequest;
use App\Http\Resources\DocumentoCierre\DocumentoCierreResource;
use App\Http\Resources\DocumentoSiriusNombre\DocumentoSiriusNombreCollection;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\DatosInteresadoModel;
use App\Models\DependenciaOrigenModel;
use App\Models\DocumentoCierreModel;
use App\Models\GestorRespuestaModel;
use App\Models\InformeCierreModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\RemisionQuejaModel;
use App\Models\User;
use ErrorException;
use Illuminate\Support\Facades\DB;
use stdClass;

class DocumentoSiriusController extends Controller
{
    private $repository;
    use LogTrait;
    use MailTrait;
    use SiriusTrait;
    use ProcesoDisciplinarioTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new DocumentoSiriusModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return DocumentoSiriusCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentoSiriusFormRequest $request)
    {
        try {
            DB::connection()->beginTransaction();

            $datosRequest = $request->validated()["data"]["attributes"];

            $datosRequest[0]['id_etapa'] =  LogTrait::etapaActual($datosRequest[0]['id_proceso_disciplinario']);

            $datos_proceso_disciplinario = $this->obtenerDatosProcesoDisciplinario($datosRequest[0]['id_proceso_disciplinario']);

            if ($datosRequest[0]['id_etapa'] == Constants::ETAPA['evaluacion'] && $datosRequest[0]['id_fase'] == Constants::FASE['documento_cierre']) { //PROCESO PARA DOCUMENTO CIERRE
                if (isset($datosRequest[0]['documento_vacio']) && $datosRequest[0]['documento_vacio']) {

                    // Guardar en el LOG
                    LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest[0]['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

                    $logRequest['id_proceso_disciplinario'] = $datosRequest[0]['id_proceso_disciplinario'];
                    $logRequest['id_etapa'] = $datosRequest[0]['id_etapa'];
                    $logRequest['id_fase'] = Constants::FASE['documento_cierre']; // Log de tipo Fase
                    $logRequest['id_tipo_log'] = 2;
                    $logRequest['descripcion'] = substr('No se adjunta documento y tampoco compulsa de copias', 0, 4000);
                    $logRequest['created_user'] = auth()->user()->name;
                    $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido']; // Remisionado
                    $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
                    $logRequest['id_funcionario_actual'] = auth()->user()->name;
                    $logRequest['id_funcionario_registra'] = auth()->user()->name;
                    $logRequest['id_funcionario_asignado'] = auth()->user()->name;
                    $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['anexo_documentos'];

                    $logModel = new LogProcesoDisciplinarioModel();
                    LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

                    if ($datos_proceso_disciplinario->id_evaluacion != 0 && $datos_proceso_disciplinario->id_evaluacion == Constants::RESULTADO_EVALUACION['remisorio_interno']) {
                        //BUSCAR JEFE DE REMISION QUEJA
                        $restultado_remision_queja = RemisionQuejaModel::where('id_proceso_disciplinario', $datosRequest[0]['id_proceso_disciplinario'])->get();

                        //BUSCAR JEFE DE LA DEPENDENCIA
                        $repository_dependencia_origen = new RepositoryGeneric();
                        $repository_dependencia_origen->setModel(new DependenciaOrigenModel());
                        $resultado_dependencia = $repository_dependencia_origen->find($restultado_remision_queja[0]->id_dependencia_destino);

                        if ($resultado_dependencia->id_usuario_jefe == null) {
                            DB::connection()->rollBack();
                            $error['estado'] = false;
                            $error['error'] = 'No es posible completar el procedimiento, la dependencia ' . $resultado_dependencia[0]->nombre . ' no tiene usuario JEFE asignado';

                            return json_encode($error);
                        }

                        //BUSCAR USUARIO
                        $repository_usuario = new RepositoryGeneric();
                        $repository_usuario->setModel(new User());
                        $resultado_usuario = $repository_usuario->find($resultado_dependencia->id_usuario_jefe);

                        $this->sendMail(
                            $resultado_usuario->email,
                            $resultado_usuario->nombre . " " . $resultado_usuario->apellido,
                            "SINPROC: (" . $datosRequest[0]['num_radicado'] . ") - VIGENCIA (" . $datosRequest[0]['vigencia'] . ')',
                            'Se realizo la fase de DOCUMENTO CIERRE de la etapa de evaluación',
                            null,
                            null,
                            null,
                        );
                    }

                    $respuesta = $this->documentoCierre($datosRequest[0]);
                    DB::connection()->commit();
                    return $respuesta;
                }
            }

            $repository_tbint_documento_sirius_descripcion = new RepositoryGeneric();
            $repository_tbint_documento_sirius_descripcion->setModel(new TbintDocumentoSiriusDescripcionModel());

            $uuid_descripcion = null;
            $descripcion = null;
            $uuid_descripcion_compulsa = null;
            $descripcion_compulsa = null;
            $path = null;

            // error_log("id_proceso_disciplinario----->: ".$datosRequest[0]['id_proceso_disciplinario']);

            $log_uuid = DB::select(
                "
                select
                    uuid
                from
                    log_proceso_disciplinario
                where id_proceso_disciplinario = '" . $datosRequest[0]['id_proceso_disciplinario'] . "'
                and id_estado = " . Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido']
            );

            $datosRequest[0]['id_log_proceso_disciplinario'] = $log_uuid[0]->uuid;

            foreach ($datosRequest as $datos) {
                if (isset($datos['es_compulsa']) && $datos['es_compulsa']) {
                    $descripcion_compulsa = $datos['descripcion'];
                } else {
                    $descripcion = $datos['descripcion'];
                }
            }

            if ($descripcion && $descripcion != null) {
                $datos['created_user'] = auth()->user()->name;
                $datos['descripcion'] = $descripcion;
                $datos['id_proceso_disciplinario'] = $datosRequest[0]['id_proceso_disciplinario'];
                $result_tbint_documento_sirius_descripcion = $repository_tbint_documento_sirius_descripcion->create($datos);
                $uuid_descripcion = $result_tbint_documento_sirius_descripcion->uuid;
            }

            if ($descripcion_compulsa && $descripcion_compulsa != null) {
                $datos['created_user'] = auth()->user()->name;
                $datos['descripcion'] = $descripcion_compulsa;
                $datos['id_proceso_disciplinario'] = $datosRequest[0]['id_proceso_disciplinario'];
                $result_tbint_documento_sirius_descripcion = $repository_tbint_documento_sirius_descripcion->create($datos);
                $uuid_descripcion_compulsa = $result_tbint_documento_sirius_descripcion->uuid;
            }

            if (!env('SUBIR_DOCUMENTACION_SIRIUS') && !env('SUBIR_DOCUMENTACION_LOCAL')) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "ERROR EN LA CONFIGURACIÓN DEL ENV POR FAVOR COMUNÍQUESE CON EL ADMINISTRADOR";
                return $error;
            }

            //Primero se contruye el protocolo de comunicacion SIRIUS - PERSONA NATURAL
            if (env('SUBIR_DOCUMENTACION_SIRIUS')) { //Valida si puede subir documentacion a sirius dada la configuracion en el ENV

                $repository_interesado = new RepositoryGeneric;
                $repository_interesado->setModel(new DatosInteresadoModel());

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
                    WHERE i.id_proceso_disciplinario = '" . $datosRequest[0]['id_proceso_disciplinario'] . "'
                    AND i.estado = " . Constants::ESTADOS['activo'] . "
                    ORDER BY i.created_at ASC
                ");

                if (!$datosRequest[0]['es_compulsa']) {
                    $siriusTrackId = $this->generarRadicado($this->generarCuerpoPeticionSirius($datosRequest, $interesado[0]));
                    if ($siriusTrackId && isset($siriusTrackId->estado)) { //Validación de que se haya recibido respuesta negativa de sirius
                        if (!$siriusTrackId->estado) {
                            return $siriusTrackId;
                        } else {
                            return "Ha ocurrido un error con SIRIUS, si el error persiste comuníquese con el Administrador";
                        }
                    }
                }
            }

            $cont = 0;

            foreach ($datosRequest as $datos) {
                $datos['id_log_proceso_disciplinario'] = $datosRequest[0]['id_log_proceso_disciplinario'];

                if (isset($datos['es_compulsa']) && $datos['es_compulsa']) {
                    $datos['created_user'] = auth()->user()->name;
                    $datos['grupo'] = $uuid_descripcion_compulsa;
                    $result_insert_documento_sirus = $this->repository->create($datos);
                } else {
                    $datos['created_user'] = auth()->user()->name;
                    $datos['es_compulsa'] = false;
                    $datos['grupo'] = $uuid_descripcion;
                    //$path = storage_path() . '/files' . '/' . $datos['vigencia'] . '/' . $datos['num_radicado'];
                    //SE AGREGA MODIFICACIÓN
                    $path = storage_path() . '/files' . '/';
                    if (
                        $datos['id_fase'] == Constants::FASE['antecedentes'] ||
                        $datos['id_fase'] == Constants::FASE['datos_interesado'] ||
                        $datos['id_fase'] == Constants::FASE['clasificacion_radicado'] ||
                        $datos['id_fase'] == Constants::FASE['entidad_investigado'] ||
                        $datos['id_fase'] == Constants::FASE['soporte_radicado']
                    ) {
                        $path .= 'captura_reparto';
                    } else if ($datos['id_fase'] == Constants::FASE['documento_cierre']) {
                        $path .= 'documento_cierre';
                    } else if ($datos['id_fase'] == Constants::FASE['informe_cierre']) {
                        $path .= 'informe_cierre';
                    }
                    //SE FINALIZA MODIFICACIÓN
                    $datos['path'] = $path;

                    $result_insert_documento_sirus = $this->repository->create($datos);

                    if (!env('OMITIR_SUBIDA_ARCHIVO')) {
                        //Guardar File
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }
                        $nombre = '';
                        if (env('SUBIR_DOCUMENTACION_SIRIUS')) {
                            $path = $path . '/' . $siriusTrackId['trackId'] . '_' . ($cont + 1) . '_' . $datos['nombre_archivo'];
                            $nombre = $siriusTrackId['trackId'] . '_' . ($cont + 1) . '_' . $datos['nombre_archivo'];
                            DocumentoSiriusModel::where('uuid', $result_insert_documento_sirus->uuid)->update(['nombre_archivo' => $nombre]);
                        } else {
                            $path = $path . '/' . $datos['nombre_archivo'];
                            $nombre = $datos['nombre_archivo'];
                        }

                        $documentos[$cont]['path'] = $path;
                        $documentos[$cont]['nombre'] = $nombre;
                        $documentos[$cont]['id_documento'] = $result_insert_documento_sirus->uuid;
                        $cont++;
                        $b64 = $datos['file64'];
                        $bin = base64_decode($b64, true);
                        file_put_contents($path, $bin);
                        //Guardar File
                    } else {
                        $cont++;
                    }
                }
            }

            if (env('SUBIR_DOCUMENTACION_SIRIUS')) { //Valida si puede subir documentacion a sirius dada la configuracion en el ENV
                if (!$datosRequest[0]['es_compulsa']) {
                    $sirius_ecmId = $this->subirDocumentoSirius($documentos, $siriusTrackId['trackId']);
                    if ($sirius_ecmId && isset($sirius_ecmId->estado)) { //Validación de que se haya recibido respuesta negativa de sirius
                        if (!$sirius_ecmId->estado) {
                            foreach ($documentos as $datos) {
                                unlink($datos['path']); //Se elimina archivo
                            }
                            return $sirius_ecmId;
                        } else {
                            return "Ha ocurrido un error al adjuntar documentos con SIRIUS, si el error persiste comuníquese con el Administrador";
                        }
                    }

                    $datosUpdate = null;
                    foreach ($documentos as $datos) {
                        $datosUpdate['sirius_ecm_id'] = $sirius_ecmId['ecmId'];
                        $datosUpdate['sirius_track_id'] = $siriusTrackId['trackId'];
                        $this->repository->update($datosUpdate, $datos['id_documento']);
                    }
                }
            }


            $log_descripcion = $descripcion;

            if ($descripcion_compulsa != null) {
                $log_descripcion = $descripcion_compulsa;
            }

            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest[0]['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

            // Guardar en el LOG
            $logRequest['id_proceso_disciplinario'] = $result_insert_documento_sirus->id_proceso_disciplinario;
            $logRequest['id_etapa'] = $result_insert_documento_sirus->id_etapa;
            $logRequest['id_fase'] =  $result_insert_documento_sirus->id_fase;
            $logRequest['id_tipo_log'] = 2;
            $logRequest['descripcion'] = substr($log_descripcion, 0, 4000);
            $logRequest['created_user'] = $result_insert_documento_sirus->created_user;
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido']; // Remisionado
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['anexo_documentos'];

            if ($datosRequest[0]['id_etapa'] == Constants::ETAPA['evaluacion'] && $datosRequest[0]['id_fase'] == Constants::FASE['documento_cierre']) {

                if (isset($datosRequest[0]['es_compulsa'])) {

                    //BUSCAR JEFE DE LA DEPENDENCIA
                    $repository_dependencia_origen = new RepositoryGeneric();
                    $repository_dependencia_origen->setModel(new DependenciaOrigenModel());
                    $resultado_dependencia = $repository_dependencia_origen->find(auth()->user()->id_dependencia);

                    if ($resultado_dependencia->id_usuario_jefe == null) {
                        $error['estado'] = false;
                        $error['error'] = 'La dependencia actual no tiene usuario JEFE asignado';

                        return json_encode($error);
                    }

                    //BUSCAR USUARIO
                    $repository_usuario = new RepositoryGeneric();
                    $repository_usuario->setModel(new User());
                    $resultado_usuario = $repository_usuario->find($resultado_dependencia->id_usuario_jefe);

                    // Se captura la informacion del usuario
                    $nombreGet = !empty($resultado_usuario->nombre) ? $resultado_usuario->nombre . " " : "";
                    $apellidoGet = !empty($resultado_usuario->apellido) ? $resultado_usuario->apellido : "";
                    $nombre_usuario = $nombreGet . $apellidoGet;

                    try {
                        $this->sendMail(
                            $resultado_usuario->email,
                            $nombre_usuario,
                            "SINPROC: (" . $datosRequest[0]['num_radicado'] . ") - VIGENCIA (" . $datosRequest[0]['vigencia'] . ')',
                            'Se realizo la fase de DOCUMENTO CIERRE de la etapa de evaluación',
                            null,
                            null,
                            null,
                        );
                    } catch (ErrorException $e) {
                        error_log($e);
                    }
                }

                if ($datos_proceso_disciplinario->id_evaluacion != 0 && $datos_proceso_disciplinario->id_evaluacion == Constants::RESULTADO_EVALUACION['remisorio_interno']) {
                    //BUSCAR JEFE DE REMISION QUEJA
                    $restultado_remision_queja = RemisionQuejaModel::where('id_proceso_disciplinario', $datosRequest[0]['id_proceso_disciplinario'])->get();

                    //BUSCAR JEFE DE LA DEPENDENCIA
                    $repository_dependencia_origen = new RepositoryGeneric();
                    $repository_dependencia_origen->setModel(new DependenciaOrigenModel());
                    $resultado_dependencia = $repository_dependencia_origen->find($restultado_remision_queja[0]->id_dependencia_destino);

                    if ($resultado_dependencia->id_usuario_jefe == null) {
                        DB::connection()->rollBack();
                        $error['estado'] = false;
                        $error['error'] = 'No es posible completar el procedimiento, la dependencia ' . $resultado_dependencia[0]->nombre . ' no tiene usuario JEFE asignado';

                        return json_encode($error);
                    }

                    //BUSCAR USUARIO
                    $repository_usuario = new RepositoryGeneric();
                    $repository_usuario->setModel(new User());
                    $resultado_usuario = $repository_usuario->find($resultado_dependencia->id_usuario_jefe);

                    $this->sendMail(
                        $resultado_usuario->email,
                        $resultado_usuario->nombre . " " . $resultado_usuario->apellido,
                        "SINPROC: (" . $datosRequest[0]['num_radicado'] . ") - VIGENCIA (" . $datosRequest[0]['vigencia'] . ')',
                        'Se realizo la fase de DOCUMENTO CIERRE de la etapa de evaluación',
                        null,
                        null,
                        null,
                    );
                }

                /*$logModel = new LogProcesoDisciplinarioModel();
                LogProcesoDisciplinarioResource::make($logModel->create($logRequest));*/
                $respuesta = DocumentoSiriusResource::make($result_insert_documento_sirus);
                $this->documentoCierre($datosRequest[0]);
            } else if ($datosRequest[0]['id_etapa'] == Constants::ETAPA['evaluacion'] && $datosRequest[0]['id_fase'] == Constants::FASE['gestor_respuesta']) {

                $repository_gestor_respuesta = new RepositoryGeneric();
                $repository_gestor_respuesta->setModel(new GestorRespuestaModel());
                $datos_gestor_respuesta['id_proceso_disciplinario'] = $datosRequest[0]['id_proceso_disciplinario'];
                $datos_gestor_respuesta['aprobado'] = true;
                $datos_gestor_respuesta['nuevo_documento'] = true;
                $datos_gestor_respuesta['created_user'] = auth()->user()->name;
                $datos_gestor_respuesta['descripcion'] = $datosRequest[0]['id_proceso_disciplinario'];
                $datos_gestor_respuesta['proceso_finalizado'] = false;

                $respuesta_gestor_respuesta = $repository_gestor_respuesta->customQuery(
                    function ($model) use ($datos_gestor_respuesta) {
                        return $model
                            ->where('id_proceso_disciplinario', $datos_gestor_respuesta['id_proceso_disciplinario'])
                            ->orderby('created_at', 'desc')
                            ->get();
                    }
                )->first();

                if ($respuesta_gestor_respuesta) {
                    $datos_gestor_respuesta['version'] = $respuesta_gestor_respuesta->version + 1;
                } else {
                    $datos_gestor_respuesta['version'] = 1;
                }

                $repository_gestor_respuesta->create($datos_gestor_respuesta);
                $respuesta = DocumentoSiriusResource::make($result_insert_documento_sirus);
            } else if ($datosRequest[0]['id_etapa'] == Constants::ETAPA['evaluacion'] && $datosRequest[0]['id_fase'] == Constants::FASE['informe_cierre']) {

                $respuesta = DocumentoSiriusResource::make($result_insert_documento_sirus);

                if (env('SUBIR_DOCUMENTACION_SIRIUS') == false) { //Valida si puede subir documentacion a sirius dada la configuracion en el ENV
                    $siriusTrackId['trackId'] = null;
                    $sirius_ecmId['ecmId'] = null;
                }

                $informe_cierre['id_proceso_disciplinario'] = $datosRequest[0]['id_proceso_disciplinario'];
                $informe_cierre['radicado_sirius'] = $siriusTrackId['trackId'];
                $informe_cierre['documento_sirius'] = $sirius_ecmId['ecmId'];
                $informe_cierre['descripcion'] = $datosRequest[0]['descripcion'];
                $informe_cierre['created_user'] = auth()->user()->name;
                $informe_cierre['id_fase'] = Constants::FASE['informe_cierre'];
                $informe_cierre['id_etapa'] = Constants::ETAPA['evaluacion'];
                $informe_cierre['id_documento_sirius'] = $respuesta->uuid;
                $informe_cierre['finalizado'] = Constants::ESTADOS['inactivo'];
                $informe_cierre['id_dependencia'] = auth()->user()->id_dependencia;

                $resultado_informe_cierre = InformeCierreModel::create($informe_cierre);
            } else {
                $respuesta = DocumentoSiriusResource::make($result_insert_documento_sirus);
            }

            $logRequest['id_fase_registro'] = $respuesta->uuid;

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            if ($datosRequest[0]['id_etapa'] == Constants::ETAPA['evaluacion'] && $datosRequest[0]['id_fase'] == Constants::FASE['informe_cierre']) {

                //EN CASO DE QUE SE NECESITE ENVIAR CORREO ELECTRONICO, SE DESHABILITO POR QUE EN LA HU NO ESTA CONTEMPLADO
                /*$restulado_informe_cierre = $this->envioCorreoInformeCierre($datosRequest[0]['id_proceso_disciplinario'], $datosRequest[0]['num_radicado'], $datosRequest[0]['vigencia'], $documentos[0]['path'], $datosRequest[0]['descripcion']);

                if(!$restulado_informe_cierre->estado){

                    foreach($datosRequest as $datos){
                        if(!env('SUBIR_DOCUMENTACION_LOCAL') && !env('OMITIR_SUBIDA_ARCHIVO')){//Valida si debe eliminar archivos dada la configuracion en el ENV
                            foreach($documentos as $datos){
                                unlink($datos['path']); //Se elimina archivo
                                $path_documento['path'] = '';
                                $this->repository->update($path_documento, $datos['id_documento']);
                            }
                        }
                    }

                    return $restulado_informe_cierre->error;
                }*/
            }

            foreach ($datosRequest as $datos) {
                if (!env('SUBIR_DOCUMENTACION_LOCAL') && !env('OMITIR_SUBIDA_ARCHIVO')) { //Valida si debe eliminar archivos dada la configuracion en el ENV
                    if (isset($documentos)) {
                        foreach ($documentos as $datos) {
                            unlink($datos['path']); //Se elimina archivo
                            $path_documento['path'] = '';
                            $this->repository->update($path_documento, $datos['id_documento']);
                        }
                    }
                }
            }

            DB::connection()->commit();
            return $respuesta;
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            dd($e);
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

    public function envioCorreoInformeCierre($id_proceso_disciplinario, $radicado, $vigencia, $path, $descripcion)
    {
        try {

            //BUSCAR JEFE SEGUN LINEAMIENTOS DE INFORME CIERRE
            //BUSCAR JEFE DE LA DEPENDENCIA
            $resultado_dependencia_actual_expediente = DependenciaOrigenModel::where('id', auth()->user()->id_dependencia)->get();

            if (count($resultado_dependencia_actual_expediente) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            } else if ($resultado_dependencia_actual_expediente[0]->id_usuario_jefe == null) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = 'No es posible completar el procedimiento, la dependencia ' . $resultado_dependencia_actual_expediente[0]->nombre . ' no tiene usuario JEFE asignado';
                return $error;
            }

            $resultado_usuario_actual_expediente = User::where('id', $resultado_dependencia_actual_expediente[0]->id_usuario_jefe)->get();

            if (count($resultado_usuario_actual_expediente) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            }

            //BUSCAR JEFE OFICINA JURIDICA DE INFORME CIERRE
            $resultado_dependencia_parametrizada = DependenciaOrigenModel::where('id', 310)->get(); //OFICINA JURIDICA ESTABLECIDA DE FORMA ESTATICA MIENTRAS SE CONSTRUYE LA PARTE ADMINISTRATIVA

            if (count($resultado_dependencia_parametrizada) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            } else if ($resultado_dependencia_parametrizada[0]->id_usuario_jefe == null) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = 'No es posible completar el procedimiento, la dependencia ' . $resultado_dependencia_parametrizada[0]->nombre . ' no tiene usuario JEFE asignado';
                return $error;
            }

            $resultado_usuario_actual_parametrizada = User::where('id', $resultado_dependencia_parametrizada[0]->id_usuario_jefe)->get();

            if (count($resultado_usuario_actual_parametrizada) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            }

            // Se captura la informacion del usuario
            $nombreGet = !empty($resultado_usuario_actual_expediente[0]->nombre) ? $resultado_usuario_actual_expediente[0]->nombre . " " : "";
            $apellidoGet = !empty($resultado_usuario_actual_expediente[0]->apellido) ? $resultado_usuario_actual_expediente[0]->apellido : "";
            $nombre_usuario = $nombreGet . $apellidoGet;

            $this->sendMail(
                $resultado_usuario_actual_expediente[0]->email,
                $nombre_usuario,
                "SINPROC: (" . $radicado . ") - VIGENCIA (" . $vigencia . ')',
                //'Se realizo respuesta de INFORME CIERRE de la etapa de evaluación',
                $descripcion,
                [$path],
                $resultado_usuario_actual_parametrizada[0]->email,
                null,
            );

            $error = new stdClass;
            $error->estado = true;
            return $error;
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            DB::connection()->rollBack();
            $error = new stdClass;
            $error->estado = false;
            $error->error = 'No fue posible enviar el correo eléctronico, si el problema persiste comuníquese con el administrador';
            return $error;
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
        return DocumentoSiriusResource::make($this->repository->find($id));
    }

    /**
     * Obtener todos los documentos registrados a un proceso disciplinario teniendo el cuenta el Id de la tabla: $procesoDiciplinarioUUID
     */
    //public function getSoporteRadicadoByIdDisciplinario($procesoDiciplinarioUUID, DocumentoSiriusFormRequest $request)
    public function getSoporteRadicadoByIdDisciplinario($procesoDiciplinarioUUID, $per_page, $current_page, $estado, $solo_sirius)
    {
        if ($solo_sirius != 'null') {
            $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID, $estado) {
                return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)
                    ->where('estado', $estado)
                    ->where('sirius_track_id', '<>', null)
                    ->where('sirius_ecm_id', '<>', null)
                    ->orderBy('created_at', 'DESC')->get();
                //->paginate($per_page, ['*'], 'documentos', $current_page);
            });
            return DocumentoSiriusCollection::make($query);
        } else {
            $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID, $estado) {
                return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)
                    ->where('estado', $estado)
                    ->orderBy('created_at', 'DESC')
                    ->get();
                //->paginate($per_page, ['*'], 'documentos', $current_page);
            });
            return DocumentoSiriusCollection::make($query);
        }
    }

    /**
     * Obtener todos los documentos registrados a un proceso disciplinario segun la el radicado (expediente) y la vigencia
     */
    public function getSoporteRadicadoByExpediente($radicado_sirius)
    {
        try {

            $query = $this->repository->customQuery(function ($model) use ($radicado_sirius) {
                return $model
                    ->where('sirius_track_id', $radicado_sirius)
                    ->get();
            });

            if (count($query) > 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = 'El número de radicado SIRIUS ya se encuentra usado en otro expediente';
                return $error;
            }

            return $this->buscarRadicado($radicado_sirius);
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
     * Obtener todos los documentos registrados a un proceso disciplinario segun la el radicado (expediente) y la vigencia
     */
    public function getSoporteRadicadoByExpedienteNotificaciones($radicado_sirius)
    {
        try {

            // Se consulta el radicado y el documento
            return $this->buscarRadicado($radicado_sirius);
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
     * Obtener todos los documentos registrados a un proceso disciplinario segun la etapa y fase
     */
    public function getSoporteRadicadoByEtapaFase($id_proceso_disciplinario, $id_etapa, $id_fase)
    {
        $query = $this->repository->customQuery(function ($model) use ($id_proceso_disciplinario, $id_etapa, $id_fase) {
            return $model
                ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
                ->where('id_etapa', $id_etapa)
                ->where('id_fase', $id_fase)
                ->get();
        });

        return DocumentoSiriusCollection::make($query);
    }


    /**
     * Obtener el nombre de todos los documentos registrados a un proceso disciplinario segun el proceso disciplinario
     */
    public function getNombresSoporteRadicadoByIdDisciplinario($procesoDiciplinarioUUID)
    {
        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID) {
            return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)->get();
        });

        return DocumentoSiriusNombreCollection::make($query);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentoSiriusUpdateFormRequest $request, $id)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRequest = $request->validated()["data"]["attributes"];

            $datosRequestVal = DocumentoSiriusResource::make($this->repository->find($id));
            $estadoFinal = (!$datosRequestVal['estado']);
            $estadoInicial = $datosRequestVal['estado'];

            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

            //registramos log
            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] =  $datosRequest['id_etapa'];
            $logRequest['id_fase'] = $datosRequest['id_fase']; // antecedentes
            $logRequest['id_tipo_log'] = 2; // Log de tipo Fase
            $logRequest['descripcion'] = 'Cambio de ' . ($estadoInicial == '0' ? "Inactivo" : "Activo") . ' a ' . ($estadoFinal == '0' ? "Inactivo" : "Activo") . ', obervaciones: ' . substr($datosRequest['estado_observacion'], 0, 3800);
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = 3; // Remisionado
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia; // Remisionado
            $logRequest['documentos'] = false;
            $logRequest['id_fase_registro'] = $id;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            $respuesta = DocumentoSiriusModel::where('UUID', $id)->update(['estado' => (!$datosRequestVal['estado'])]);

            DB::connection()->commit();
            return $respuesta;
        } catch (QueryException  $e) {

            $error['estado'] = false;
            $error['error'] = $e->getMessage();
            return json_encode($error);
        }
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

    public function getDocumento(DocumentoSiriusDescargaFormRequest $request)
    {
        try {
            $datosRequest = $request->validated()["data"]["attributes"];
            if (isset($datosRequest['consulta_sirius']) && isset($datosRequest['versionLabel']) && $datosRequest['consulta_sirius'] == true && $datosRequest['versionLabel']) {
                return $this->buscarDocumento($datosRequest['id_documento_sirius'], $datosRequest['versionLabel']);
            }

            $resultado_documento_sirius = $this->repository->customQuery(
                function ($model) use ($datosRequest) {
                    return $model
                        ->where('uuid', $datosRequest['id_documento_sirius'])
                        ->orderby('created_at', 'asc')
                        ->get();
                }
            )->first();

            if (!$resultado_documento_sirius) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "No es posible descargar el documento";
                return $error;
            }

            //if($resultado_documento_sirius && $resultado_documento_sirius->path && env('SUBIR_DOCUMENTACION_SIRIUS') == false){
            if ($resultado_documento_sirius->sirius_track_id == null) {
                $path = $resultado_documento_sirius->path . '/' . $resultado_documento_sirius->nombre_archivo;

                $datos["base_64"] = base64_encode(file_get_contents($path));

                switch ($datosRequest['extension']) {
                    case 'doc':
                        $type = "content-type: application/msword";
                        break;
                    case 'pdf':
                        $type = "content-type: application/pdf";
                        break;
                    case 'xls':
                        $type = "content-type: application/xls";
                        break;
                    case 'zip':
                        $type = "content-type: application/zip";
                        break;
                    case 'rar':
                        $type = "content-type: application/x-rar-compressed";
                        break;
                    case 'jpg':
                        $type = "content-type: application/jpeg";
                        break;
                    case 'avi':
                        $type = "content-type: application/x-msvideo";
                        break;
                    case 'mpeg':
                        $type = "content-type: application/mpeg";
                        break;
                    case 'wav':
                        $type = "content-type: application/x-wav";
                        break;
                    default:
                        $type = 'Content-Type: application/octet-stream';
                        break;
                }

                $datos['content_type'] = $type;

                return json_encode($datos);
            } else {
                $documetos_lista = $this->buscarRadicado($resultado_documento_sirius['sirius_track_id']);

                $documento = null;

                for ($cont = 0; $cont < count($documetos_lista['documentoDTOList']); $cont++) {
                    if ($documetos_lista['documentoDTOList'][$cont]['nombreDocumento'] == $resultado_documento_sirius['nombre_archivo']) {
                        $documento = $documetos_lista['documentoDTOList'][$cont];
                        $cont = count($documetos_lista['documentoDTOList']) + 1;
                    }
                }

                if ($documento) {
                    return $this->buscarDocumento($documento['idDocumento'], $documento['versionLabel']);
                } else {
                    $error = new stdClass;
                    $error->estado = false;
                    $error->error = "No es posible descargar el documento";
                    return $error;
                }
            }
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

        /*$datosRequest = $request->validated();

            if(isset($datosRequest['consulta_sirius']) && isset($datosRequest['versionLabel']) && $datosRequest['consulta_sirius'] == true && $datosRequest['versionLabel']){
                return $this->buscarDocumento($datosRequest['id_documento_sirius'], $datosRequest['versionLabel']);
            }
            else if($datosRequest['es_compulsa'] == true){

                $resultado_documento_sirius = $this->repository->customQuery(function ($model) use ($datosRequest)
                {
                    return $model
                    ->where('uuid', $datosRequest['id_documento_sirius'])
                    ->orderby('created_at', 'asc')
                    ->get();
                })->first();

                $documetos_lista = $this->buscarRadicado($resultado_documento_sirius['sirius_track_id']);

                $documento = null;

                for($cont = 0; $cont < count($documetos_lista['documentoDTOList']); $cont++){
                    if($documetos_lista['documentoDTOList'][$cont]['nombreDocumento'] == $resultado_documento_sirius['nombre_archivo']){
                        $documento = $documetos_lista['documentoDTOList'][$cont];
                        $cont = count($documetos_lista['documentoDTOList']) + 1;
                    }
                }

                if($documento){
                    return $this->buscarDocumento($documento['idDocumento'], $documento['versionLabel']);
                }
                else{
                    $error = new stdClass;
                    $error->estado = false;
                    $error->error = "No es posible descargar el documento";
                    return $error;
                }


            }
            else{

                $resultado_documento_sirius = $this->repository->customQuery(function ($model) use ($datosRequest)
                {
                    return $model
                    ->where('uuid', $datosRequest['id_documento_sirius'])
                    ->orderby('created_at', 'asc')
                    ->get();
                })->first();

                if($resultado_documento_sirius->path){
                    $path = storage_path() . '/files' . '/' . $datosRequest['vigencia'] . '/' . $datosRequest['radicado'] . '/'. $resultado_documento_sirius['nombre_archivo'];
                }
                else{
                    $documetos_lista = $this->buscarRadicado($resultado_documento_sirius['sirius_track_id']);

                    $documento = null;

                    for($cont = 0; $cont < count($documetos_lista['documentoDTOList']); $cont++){
                        if($documetos_lista['documentoDTOList'][$cont]['nombreDocumento'] == $resultado_documento_sirius['nombre_archivo']){
                            $documento = $documetos_lista['documentoDTOList'][$cont];
                            $cont = count($documetos_lista['documentoDTOList']) + 1;
                        }
                    }

                    if($documento){
                        return $this->buscarDocumento($documento['idDocumento'], $documento['versionLabel']);
                    }
                    else{
                        $error = new stdClass;
                        $error->estado = false;
                        $error->error = "No es posible descargar el documento";
                        return $error;
                    }
                }

            }

            $datos["base_64"] = base64_encode(file_get_contents($path));

            switch($datosRequest['extension']){
                case 'doc':
                    $type = "content-type: application/msword";
                    break;
                case 'pdf':
                    $type = "content-type: application/pdf";
                    break;
                case 'xls':
                    $type = "content-type: application/xls";
                    break;
                case 'zip':
                    $type = "content-type: application/zip";
                    break;
                case 'rar':
                    $type = "content-type: application/x-rar-compressed";
                    break;
                case 'jpg':
                    $type = "content-type: application/jpeg";
                    break;
                case 'avi':
                    $type = "content-type: application/x-msvideo";
                    break;
                case 'mpeg':
                    $type = "content-type: application/mpeg";
                    break;
                case 'wav':
                    $type = "content-type: application/x-wav";
                    break;
                default:
                    $type = 'Content-Type: application/octet-stream';
                    break;
            }

            $datos['content_type'] = $type;

            return json_encode($datos);

        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }*/
    }

    /**
     * Trae los documentos asociados al id_log_proceso_disciplinario. Esta información se muestra en el Log de Etapa.
     *
     * @param  string $IdLogProcesoDisciplinario
     * @return \Illuminate\Http\Response
     */
    public function getDocumentosByIdLogProcesoDisciplinario($idLogProcesoDisciplinario)
    {

        $query = $this->repository->customQuery(function ($model) use ($idLogProcesoDisciplinario) {
            return $model->where('id_log_proceso_disciplinario', $idLogProcesoDisciplinario)
                ->orderBy('created_at', 'desc')
                ->get();
        });

        return DocumentoSiriusCollection::make($query);
    }

    private function documentoCierre($datosRequest)
    {

        try {
            DB::connection()->beginTransaction();

            $datos_registrar['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $datos_registrar['estado'] = true;
            $datos_registrar['seguimiento'] = $datosRequest['seguimiento'];
            $datos_registrar['descripcion_seguimiento'] = $datosRequest['descripcion_seguimiento'];
            $datos_registrar['eliminado'] = false;

            $repository_documento_cierre = new RepositoryGeneric();
            $repository_documento_cierre->setModel(new DocumentoCierreModel());
            $respuesta_query = $repository_documento_cierre->create($datos_registrar);

            DB::connection()->commit();
            return DocumentoCierreResource::make($respuesta_query);
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            dd($e);

            $error['estado'] = false;
            $error['error'] = 'El archivo que está adjuntando es mayor de lo que permitido por el sistema.';

            return json_encode($error);
        }
    }
}
