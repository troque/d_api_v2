<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrazabilidadActuacionesModel;
use App\Models\ActuacionesModel;
use App\Models\MasActuacionesModel;
use App\Models\ArchivoActuacionesModel;
use App\Models\EtapaModel;
use App\Models\DependenciaOrigenModel;
use App\Http\Requests\TrazabilidadActuacionesFormRequest;
use App\Http\Resources\TrazabilidadActuaciones\TrazabilidadActuacionesCollection;
use App\Http\Resources\TrazabilidadActuaciones\TrazabilidadActuacionesResource;
use Illuminate\Support\Facades\DB;
use App\Repositories\RepositoryGeneric;
use App\Models\ProcesoDiciplinarioModel;
use App\Services\WordServices;
use App\Http\Controllers\Api\MasActuacionesController;
use App\Http\Controllers\Traits\ActuacionesTrait;
use App\Http\Controllers\Traits\MailTrait;
use App\Http\Controllers\Traits\SemaforoTrait;
use App\Http\Controllers\Traits\TrazabilidadTrait;
use App\Models\MasConsecutivoActuacionesModel;
use App\Models\VigenciaModel;
use App\Http\Resources\MasConsecutivoActuaciones\MasConsecutivoActuacionesResource;
use App\Http\Utilidades\Constants;
use App\Models\ProcesoDisciplinarioPorSemaforoModel;
use App\Models\User;

class TrazabilidadActuacionesController extends Controller
{
    use MailTrait;
    use SemaforoTrait;
    use TrazabilidadTrait;
    use ActuacionesTrait;
    private $repository;
    private $wordService;
    private $masActuaciones;

    public function __construct(RepositoryGeneric $repository, WordServices $wordService, MasActuacionesController $masActuaciones)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TrazabilidadActuacionesModel());
        $this->wordService = $wordService;
        $this->masActuaciones = $masActuaciones;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TrazabilidadActuacionesCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TrazabilidadActuacionesFormRequest $request)
    {
        try {
            // Se inicializa la conexion
            DB::connection()->beginTransaction();

            // Se capturan los datos
            $datosRequest = $request->validated()["data"]["attributes"];

            $enviarCorreo = isset($datosRequest['envia_correo']) ? $datosRequest['envia_correo'] : null;
            $estadoActuacion = ($datosRequest["id_estado_actuacion"] == 1 ? "Aprobada" : ($datosRequest["id_estado_actuacion"] == 2 ? "Rechazada" : ""));
            $idProcesoDisciplinario = isset($datosRequest['id_proceso_disciplinario']) ? $datosRequest['id_proceso_disciplinario'] : null;
            $uuidActuacion = isset($datosRequest["uuid_actuacion"]) ? $datosRequest["uuid_actuacion"] : "";
            $despuesAprobacionListarActuacion = "";

            // Se valida que se este utilizando la funcion en el momento de aprobar o rechazar una actuacion
            if (!empty($enviarCorreo) && $enviarCorreo == 1) {

                // Se consultan los datos de la actuacion creada por el uuid
                $actuacionesData = ActuacionesModel::where('uuid', $datosRequest['uuid_actuacion'])->get();

                $idActuacion = $actuacionesData[0]["id_actuacion"];
                $numeroAuto = $actuacionesData[0]["auto"];

                // Se consultan los datos de la actuacion por el id
                $actuacionesMaestraData = MasActuacionesModel::where('id', $idActuacion)->get();

                $nombreActuacion = $actuacionesMaestraData[0]["nombre_actuacion"];
                $idEtapaDespuesAprobacion = $actuacionesData[0]["id_etapa_siguiente"];
                $idEtapa = $actuacionesData[0]["id_etapa"];
                $etapaHabilitada = $actuacionesMaestraData[0]["etapa_siguiente"];
                $despuesAprobacionListarActuacion = $actuacionesMaestraData[0]["despues_aprobacion_listar_actuacion"];
                $idEtapasValidos = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
                $consecutivo = "";
                $datosRequestObservacion = "";
                $tipoArchivoActuacion = 1;

                $archivoActuacionesData = ArchivoActuacionesModel::where('uuid_actuacion', $uuidActuacion)->where("id_tipo_archivo", $tipoArchivoActuacion)->get();

                $uuidArchivoActuacion = $archivoActuacionesData[0]["uuid"];
                $documentoRutaInicial = $archivoActuacionesData[0]["documento_ruta"];
                $nombreArchivo = $archivoActuacionesData[0]["nombre_archivo"];

                $procesoDisciplinario = ProcesoDiciplinarioModel::where('uuid', $idProcesoDisciplinario)->first();

                // Se valida que el proceso sea aprobada
                if ($estadoActuacion == "Aprobada") {

                    //SI ES COMISORIO PERMITE ESTABLECER EL USUARIO COMISIONADO DEFINITIVO
                    if ($actuacionesMaestraData[0]->tipo_actuacion == Constants::TIPO_ACTUACION['comisorio']) {
                        $respuesta = ProcesoDiciplinarioModel::where('uuid', $idProcesoDisciplinario)->update(['usuario_comisionado' => $procesoDisciplinario->temporal_usuario_comisionado, 'temporal_usuario_comisionado' => null]);
                    }

                    // Se valida si la etapa es aprobada para actualizar su estado
                    if (!empty($idEtapaDespuesAprobacion) && in_array($idEtapaDespuesAprobacion, $idEtapasValidos) && $etapaHabilitada) {
                        $respuesta = ProcesoDiciplinarioModel::where('UUID', $idProcesoDisciplinario)->update(['id_etapa' => $idEtapaDespuesAprobacion]);
                        $datosRequestObservacion = "Se registro una modificacion en la actuación $nombreActuacion (La actuacion ha sido modificada con estado " . $estadoActuacion . ").";
                    } else {
                        $datosRequestObservacion = "Se registro una modificacion en la actuación $nombreActuacion (La actuacion ha sido modificada con estado " . $estadoActuacion . ").";
                    }

                    // Se actualiza el estado en la tabla principal de actuaciones a 1 que es aprobado
                    ActuacionesModel::where('UUID', $datosRequest['uuid_actuacion'])->update(['id_estado_actuacion' => ($datosRequest['id_estado_actuacion'])]);

                    // Se captura la informacion del prefijo
                    $dependenciaId = auth()->user()->id_dependencia;
                    $dataUsuario = DependenciaOrigenModel::where('id',  $dependenciaId)->get();
                    $prefijo = $dataUsuario[0]->prefijo;

                    // Se captura la fecha
                    $fecha = date("Y");

                    // Se consultan los datos de la vigencia por el año
                    $datosVigencia = VigenciaModel::where('vigencia', $fecha)->get()->first();

                    $idVigencia = $datosVigencia["id"];

                    // Se captura la informacion del consecutivo
                    $datosConsecutivo = MasConsecutivoActuacionesModel::where('id_vigencia', $idVigencia)->where('estado', true)->get()->first();

                    if ($datosConsecutivo == null) {
                        $error['estado'] = false;
                        $error['error'] = 'Aún no se encuentra un consecutivo de auto registrado para la vigencia ' . $datosVigencia["vigencia"];
                        return json_encode($error);
                    }

                    $consecutivoId = $datosConsecutivo["id"];
                    $consecutivoActuaciones = intval($datosConsecutivo["consecutivo"]) + 1;

                    // Se valida cuando no hay prefijo
                    if (!empty($prefijo)) {
                        // Se concadena el consecutivo a insertar
                        $consecutivo = $prefijo . "-" . $fecha . "-" . $consecutivoActuaciones;
                    } else {
                        // Se concadena el consecutivo a insertar
                        $consecutivo = $fecha . "-" . $consecutivoActuaciones;
                    }

                    /*if ($actuacionesMaestraData[0]->generar_auto == '1') {
                        $consecutivo = $consecutivoActuaciones;
                    }

                    error_log("CONSECUTIVOOOOOOO: " . $consecutivo);
*/

                    // Se valida que haya consecutivo
                    if (!empty($consecutivo)) {

                        error_log("CONSECUTIVOOOOOOO: " . $consecutivo);
                        error_log("GENERAR AUTO: " . $actuacionesMaestraData[0]->generar_auto);


                        if ($actuacionesMaestraData[0]->generar_auto == '1') {

                            $rutaArchivoInicial = storage_path() . $documentoRutaInicial;
                            $parametros = [
                                ["param" => '${numero_de_auto}', "value" => $consecutivo]
                            ];

                            $actualizarParamsArchivo = $this->wordService->replaceDocumentParamsArchivo($rutaArchivoInicial, $parametros, $nombreArchivo);
                            $documentoActualizar = $actualizarParamsArchivo;

                            // Se actualiza la ruta del documento en la tabla de actuaciones y en la tabla de trazabilidad del archivo de las actuaciones
                            ArchivoActuacionesModel::where('UUID', $uuidArchivoActuacion)->update(['documento_ruta' => $documentoActualizar]);
                            ActuacionesModel::where("UUID", $uuidActuacion)->update(['documento_ruta' => $documentoActualizar]);
                            $mensajeActualizado = "Se ha generado el auto o consecutivo al documento inicial";

                            // Se inserta un registro en la trazabilidad de la actuacion
                            $datosRequestObservacion1 = "Se registro una modificacion en la actuación $nombreActuacion (" . $mensajeActualizado . ").";
                            $datosRequest['uuid_actuacion'] = $datosRequest['uuid_actuacion'];
                            $datosRequest['estado'] = $datosRequest['estado'];
                            $datosRequest['created_user'] = auth()->user()->nombre;
                            $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;
                            $datosRequest["observacion"] = $datosRequestObservacion1;

                            // Se manda el array del modelo con su informacion para crearlo en su tabla
                            TrazabilidadActuacionesResource::make($this->repository->create($datosRequest));

                            //  Se valida cuando el numero del auto es vacio se actualiza en caso de
                            //  Que falle el servicio de firma no vuelva a actualizar el auto
                            if (empty($numeroAuto)) {
                                // Se actualiza el auto y la etapa a cambiar en la tabla actuaciones
                                ActuacionesModel::where('UUID', $datosRequest['uuid_actuacion'])->update(['auto' => ($consecutivo)]);
                            }
                        }

                        // Se valida que se haya actualizado la etapa del proceso disciplinario y se inserta un registro de trazabilidad con el proceso cambiante
                        if (isset($actualizacionEtapa) && $actualizacionEtapa && !empty($idEtapaDespuesAprobacion) && in_array($idEtapaDespuesAprobacion, $idEtapasValidos)) {

                            $informacionEtapa = EtapaModel::where([
                                ['id', '=', $idEtapaDespuesAprobacion]
                            ])->get();
                            $nombreEtapaCambiada = $informacionEtapa[0]->nombre;

                            // Campos de la tabla
                            $datosRequestCambioEtapa['uuid_actuacion'] = $datosRequest['uuid_actuacion'];
                            $datosRequestCambioEtapa['estado'] = $datosRequest['estado'];
                            $datosRequestCambioEtapa['created_user'] = auth()->user()->nombre;
                            $datosRequestCambioEtapa['id_dependencia'] = auth()->user()->id_dependencia;
                            $datosRequestCambioEtapa['id_estado_actuacion'] = $datosRequest['id_estado_actuacion'];
                            $datosRequestCambioEtapa['observacion'] = "La actuación $nombreActuacion, ha sido cambiada a la etapa $nombreEtapaCambiada.";

                            // Se manda el array del modelo con su informacion para crearlo en su tabla
                            TrazabilidadActuacionesResource::make($this->repository->create($datosRequestCambioEtapa));
                        }

                        // Se llama el metodo encargado de insertar el documento definitivo
                        $documentoDefinitivo = $this->actuacionFirmadaAprobada($uuidActuacion);

                        // Se valida que haya sido correcto la inserción
                        if (isset($documentoDefinitivo["estado"]) && !$documentoDefinitivo["estado"]) {

                            // Se inicializa la variable para el mensaje
                            $mensaje = "";
                            // Se valida que exista un mensaje de error
                            if (isset($documentoDefinitivo["data"][0]["rutaPdf"])) {

                                // Se reedeclara la variable
                                $mensaje = " (" . $documentoDefinitivo["data"][0]["rutaPdf"] . ").";
                            } else {

                                // Se reedeclara la variable
                                $mensaje = ".";
                            }

                            // Se retorna el error
                            return [
                                "error" => "Hubo un error al tratar de generar el documento definitivo en pdf" . $mensaje
                            ];
                        }
                    }

                    $datosRequest['observacion'] = $datosRequestObservacion;

                    //FUNCIONALIDAD PARA SEMAFOROS
                    //Obtener los semaforos correspondientes a la etapa
                    if (isset($datosRequest['validar_semaforos']) && $datosRequest['validar_semaforos']) {

                        //Obtener todos los semaforos del proceso
                        $semaforosActuacion = $this->obtenerSemaforosActuacionesPorEtapaYMasActuacion($datosRequest['id_mas_actuacion'], $idEtapa);

                        //Finalizar Semaforos
                        if (count($semaforosActuacion) > 0) {
                            foreach ($semaforosActuacion as $semaforo) {
                                $this->finalizarSemaforo($semaforo->id, $datosRequest['uuid_actuacion'], null, null);
                            }
                        }

                        //Crear nuevos Semaforos
                        $semaforosNuevosActuacion = $this->obtenerSemaforosQueInicianActuacion($datosRequest['id_mas_actuacion'], $idEtapa);

                        //dd($semaforosNuevosActuacion);

                        if (count($semaforosNuevosActuacion) > 0) {
                            foreach ($semaforosNuevosActuacion as $semaforo) {
                                $semaforoNuevo['id_semaforo'] = $semaforo->id;
                                $semaforoNuevo['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
                                $semaforoNuevo['id_actuacion'] = $datosRequest['uuid_actuacion'];
                                $semaforoNuevo['fecha_inicio'] = date('Y-m-d h:i:s');
                                $semaforoNuevo['estado'] = Constants::ESTADOS['activo'];
                                $semaforoNuevo['created_user'] = auth()->user()->name;
                                //dd($semaforoNuevo);
                                $this->iniciarSemaforo($semaforoNuevo);
                            }
                        }
                    }

                    $this->anularActuaciones($datosRequest['uuid_actuacion']);
                } else if ($estadoActuacion == "Rechazada") {

                    //SI ES COMISORIO PERMITE ESTABLECER EL USUARIO COMISIONADO DEFINITIVO
                    if ($actuacionesMaestraData[0]->tipo_actuacion == Constants::TIPO_ACTUACION['comisorio']) {
                        $respuesta = ProcesoDiciplinarioModel::where('uuid', $idProcesoDisciplinario)->update(['temporal_usuario_comisionado' => null]);
                    }

                    $mensajeValidar = $datosRequest["id_estado_actuacion"] == 1 ? 'Aprobada' : 'Rechazada';
                    $contenidoMensaje = $datosRequest['observacion'];
                    ActuacionesModel::where('UUID', $datosRequest['uuid_actuacion'])->update(['id_estado_actuacion' => ($datosRequest['id_estado_actuacion'])]);
                    $datosRequest['observacion'] = $contenidoMensaje;
                } else {
                    $contenidoMensaje = $datosRequest['observacion'];
                    $datosRequest['observacion'] = $contenidoMensaje;
                }
            } else {
                $datosRequest['observacion'] = isset($datosRequest['observacion']) ? $datosRequest['observacion'] : "";
            }

            // Campos de la tabla
            $datosRequest['uuid_actuacion'] = $datosRequest['uuid_actuacion'];
            $datosRequest['estado'] = $datosRequest['estado'];
            $datosRequest['created_user'] = auth()->user()->nombre;
            $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;
            $datosRequest['id_estado_actuacion'] = $datosRequest['id_estado_actuacion'];

            // Se manda el array del modelo con su informacion para crearlo en su tabla
            $respuesta = TrazabilidadActuacionesResource::make($this->repository->create($datosRequest));
            $respuesta = array($respuesta);
            $array = json_decode(json_encode($respuesta));
            $array[0]->attributes->despues_aprobacion_listar_actuacion = $despuesAprobacionListarActuacion;

            // Se actualiza el consecutivo por el que se encuentra
            if ($estadoActuacion == "Aprobada") {
                MasConsecutivoActuacionesModel::where('ID', $consecutivoId)->update(['CONSECUTIVO' => ($consecutivoActuaciones)]);
            }

            //PROCESO DE CAMBIO DE USUARIO SI APLICA
            $mensaje = [];
            $envioProceso = null;

            if (isset($datosRequest['rechazo'])) {
                if ($datosRequest['rechazo']) {

                    $cambio_usuario = $this->getUsuarioCreadorActuacion($datosRequest['uuid_actuacion'], $datosRequest['id_proceso_disciplinario']);
                    if (count($cambio_usuario) <= 0) {

                        $mensaje[0] = "EL USUARIO QUE CREO ESTA ACTUACIÓN NO ESTÁ HABILITADO PARA RECIBIR ESTE PROCESO POR ALGUNA DE LAS SIGUIENTES RAZONES:";
                        $mensaje[1] = "1.   EL USUARIO SE ENCUENTRA INACTIVO.";
                        $mensaje[2] = "2.   EL USUARIO NO ESTÁ HABILITADO PARA RECIBIR PROCESOS.";
                        $mensaje[3] = "3.   EL USUARIO NO ESTÁ HABILITADO PARA RECIBOS PROCESOS DISCIPLINARIOS.";
                        $mensaje[4] = "4.   EL USUARIO HA CAMBIADO DE DEPENDENCIA.";
                        $mensaje[5] = "POR LO TANTO QUEDARÁ EN LA BANDEJA DE SUS PENDIENTES.";
                    } else {
                        $datosEnvio['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
                        $datosEnvio['id_dependencia_origen'] = $cambio_usuario[0]->id_dependencia;
                        $datosEnvio['usuario_a_remitir'] = $cambio_usuario[0]->name;
                        $datosEnvio['descripcion_a_remitir'] = $datosRequest['observacion'];
                        $datosEnvio['id_etapa'] = $idEtapa;
                        $envioProceso = $this->enviarProcesoDisciplinarioAUsuario($datosEnvio);

                        try {
                            $usuarioARemitir = User::where('name', $datosEnvio['usuario_a_remitir'])->first();
                            $procesoDisciplinario = ProcesoDiciplinarioModel::where('uuid', $datosEnvio['id_proceso_disciplinario'])->first();

                            $correos = $usuarioARemitir->email;
                            $asunto = "SINPROC: (" . $procesoDisciplinario->radicado . ") - VIGENCIA (" . $procesoDisciplinario->vigencia . ')';
                            $contenido = "Se ha sido asignado el siguiente proceso disciplinario. SINPROC: (" . $procesoDisciplinario->radicado . ") - VIGENCIA (" . $procesoDisciplinario->vigencia . '), ' . substr($datosEnvio['descripcion_a_remitir'], 0, 4000);
                            $archivos = null;
                            $correoscc = null;
                            $correosbbc = null;

                            // Se captura la informacion del usuario
                            $nombreGet = !empty($usuarioARemitir->nombre) ? $usuarioARemitir->nombre . " " : "";
                            $apellidoGet = !empty($usuarioARemitir->apellido) ? $usuarioARemitir->apellido : "";
                            $nombre_usuario = $nombreGet . $apellidoGet;

                            MailTrait::sendMail(
                                $correos,
                                $nombre_usuario,
                                $asunto,
                                $contenido,
                                $archivos,
                                $correoscc,
                                $correosbbc
                            );
                        } catch (\Exception $th) {
                            error_log($th);
                        }
                    }
                }
            }

            // Se guarda la ejecucion con un commit para que se ejecute
            DB::connection()->commit();

            $respuestaJson['trazabilidad'] = $array[0];
            if (isset($datosRequest['rechazo'])) {
                if ($datosRequest['rechazo']) {
                    $respuestaJson['usuario'] = $envioProceso;
                    $respuestaJson['mensaje'] = $mensaje;
                }
            }

            // Se retorna la respuesta
            return json_encode($respuestaJson);
        } catch (\Exception $e) {
            error_log($e);
            DB::connection()->rollBack();
            dd($e);
            // Woopsy
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
     * Método que lista todas las trazabilidades por actuacion que se han hecho en el sistema
     *
     */
    public function getAllTrazablidadesActuacionesById($uuid_actuacion)
    {

        $query = $this->repository->customQuery(function ($model) use ($uuid_actuacion) {
            return $model->select(
                "trazabilidad_actuaciones.uuid as id",
                "trazabilidad_actuaciones.id_estado_actuacion",
                "m.nombre as nombre",
                "trazabilidad_actuaciones.observacion",
                "trazabilidad_actuaciones.estado",
                "trazabilidad_actuaciones.created_at",
                "trazabilidad_actuaciones.created_user",
                "trazabilidad_actuaciones.updated_user",
                "trazabilidad_actuaciones.id_dependencia",
            )
                ->join('actuaciones a', 'a.uuid', '=', 'trazabilidad_actuaciones.uuid_actuacion')
                ->join('mas_estado_actuaciones m', 'm.id', '=', 'trazabilidad_actuaciones.id_estado_actuacion')
                ->where('trazabilidad_actuaciones.uuid_actuacion', $uuid_actuacion)
                ->orderBy('trazabilidad_actuaciones.created_at', 'desc')
                ->get();
        });

        return TrazabilidadActuacionesCollection::make($query);
    }
}
