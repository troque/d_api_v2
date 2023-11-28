<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActuacionesModel;
use App\Http\Requests\ActuacionesFormRequest;
use App\Http\Requests\AgregarUsuarioFirmaMecanicaFormRequest;
use App\Http\Resources\Actuaciones\ActuacionesCollection;
use App\Http\Resources\ArchivoActuaciones\ArchivoActuacionesCollection;
use App\Http\Resources\Actuaciones\ActuacionesResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Resources\ArchivoActuaciones\ArchivoActuacionesResource;
use Illuminate\Support\Facades\DB;
use App\Repositories\RepositoryGeneric;
use App\Models\ArchivoActuacionesModel;
use App\Models\TrazabilidadActuacionesModel;
use App\Models\MasActuacionesModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\FirmaActuacionesModel;
use App\Models\User;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\TipoFirmaModel;
use App\Http\Resources\TrazabilidadActuaciones\TrazabilidadActuacionesResource;
use App\Http\Resources\TrazabilidadActuaciones\TrazabilidadActuacionesCollection;
use App\Http\Resources\FirmaActuaciones\FirmaActuacionesResource;
use App\Http\Resources\FirmaActuaciones\FirmaActuacionesCollection;
use App\Http\Utilidades\Constants;
use App\Http\Controllers\Traits\LogTrait;
use App\Services\WordServices;
use App\Http\Controllers\Traits\MailTrait;
use App\Http\Requests\ActuacionesInactivasFormRequest;
use App\Http\Resources\ActuacionesProcesoDisciplinario\ActuacionesProcesoDisciplinarioResource;
use App\Http\Resources\ActuacionesInactivasActuacion\ActuacionesInactivasActuacionCollection;
use App\Http\Resources\ActuacionesMigradas\ActuacionesMigradasCollection;
use App\Http\Resources\FirmaActuaciones\DocumentosParaFirmaCollection;
use App\Http\Resources\MasActuacionesEtapa\ActuacionesEtapaCollection;
use App\Http\Resources\Usuario\UsuarioResource;
use App\Models\ActuacionesMigradasModel;
use App\Models\ActuacionInactivaModel;
use App\Models\DependenciaOrigenModel;
use App\Models\MasEstadoActuacionesModel;
use App\Models\TrazabilidadActuacionesAnuladasModel;
use App\Models\UserRolesModel;

class ActuacionesController extends Controller
{
    use MailTrait;

    private $repository;
    private $wordService;


    public function __construct(RepositoryGeneric $repository, WordServices $wordService)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ActuacionesModel());
        $this->wordService = $wordService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->repository->customQuery(function ($model) {
            return $model->whereNull('eliminado')
                ->orWhere('eliminado', '0')
                ->orderBy('created_at', 'desc')
                ->get();
        });

        return ActuacionesCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActuacionesFormRequest $request)
    {
        try {

            // Se inicializa la conexion
            DB::connection()->beginTransaction();

            // Se capturan los datos
            $datosRequest = $request->validated()["data"]["attributes"];

            // Se captura la fecha
            $año = date("Y");
            $mes = date("m");
            $dia = date("d");
            $hor = date("h");
            $min = date("i");
            $sec = date("s");
            $actuacionesNombreCarpeta = Constants::ACTUACIONES_NOMBRE_CARPETA;

            // Se valida el archivo
            //$rutaRelativaArchivo = '/files' . '/' . $actuacionesNombreCarpeta . '/' . $año . '/' . $mes . '/' . $dia . '/' . $año . $mes . $dia . $hor . $min . $sec . '_' . $datosRequest['nombre_archivo'];  //MODIFICACION ANTERIOR
            $rutaRelativaArchivo = '/files' . '/' . $actuacionesNombreCarpeta . '/' . $año . $mes . $dia . $hor . $min . $sec . '_' . $datosRequest['nombre_archivo'];   //MODIFICACION NUEVA
            $rutaCompleta = storage_path() . $rutaRelativaArchivo;
            //$path = storage_path() . '/files' . '/' . $actuacionesNombreCarpeta . '/' . $año . '/' . $mes . '/' . $dia;  //MODIFICACION ANTERIOR
            $path = storage_path() . '/files' . '/' . $actuacionesNombreCarpeta;   //MODIFICACION NUEVA

            // Se valida que si no existe se crea la carpeta
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            // Campos de la tabla
            $datosRequest['id_actuacion'] = $datosRequest['id_actuacion'];
            $datosRequest['usuario_accion'] = "";
            $datosRequest['id_estado_actuacion'] = $datosRequest['id_estado_actuacion'];
            $datosRequest['documento_ruta'] = $rutaRelativaArchivo;
            $datosRequest["estado"] = true;
            $datosRequest['created_user'] = auth()->user()->name;
            $datosRequest['updated_user'] = "";
            $datosRequest['updated_at'] = "";
            $datosRequest['uuid_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $datosRequest['id_etapa'] = $datosRequest['id_etapa'];
            $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;
            $datosRequest["campos_finales"] = isset($datosRequest["campos_finales"]) ? json_encode($datosRequest["campos_finales"]) : [];
            $datosRequest["id_estado_visibilidad"] = Constants::ESTADOS_VISIBILIDAD['visible_todos'];

            $b64 = $datosRequest['fileBase64'];
            $bin = base64_decode($b64, true);
            file_put_contents($rutaCompleta, $bin);

            // Se manda el array del modelo con su informacion para crearlo en su tabla
            $respuesta = ActuacionesResource::make($this->repository->create($datosRequest));
            $array = json_decode(json_encode($respuesta));

            // Se captura el uuid y el documento generado por la tabla
            $uuid = $array->id;
            $documento = $array->attributes->documento_ruta;
            $codigo_tipo_archivo = "DOCINI";
            $consultaIdTipoArchivo = $this->consultarIdTipoArchivo($codigo_tipo_archivo);
            $id_tipo_archivo = $consultaIdTipoArchivo[0]->id;
            $datosRequestTipoArchivo["uuid_actuacion"] = $uuid;
            $datosRequestTipoArchivo["id_tipo_archivo"] = $id_tipo_archivo;
            $datosRequestTipoArchivo["nombre_archivo"] = $documento;
            $datosRequestTipoArchivo['created_user'] = auth()->user()->name;
            $datosRequestTipoArchivo["nombre_archivo"] = $datosRequest["nombre_archivo"];
            $datosRequestTipoArchivo["extension"] = $datosRequest["ext"];
            $datosRequestTipoArchivo["peso"] = $datosRequest["peso"];
            $datosRequestTipoArchivo["documento_ruta"] = $rutaRelativaArchivo;

            // Se manda el array del modelo con su informacion para crearlo en su tabla
            $ArchivoActuacionesModel = new ArchivoActuacionesModel();
            ArchivoActuacionesResource::make($ArchivoActuacionesModel->create($datosRequestTipoArchivo));

            // Se crea los datos para la tabla de trazabilidad de las actuaciones
            $uuidActuacion = $array->id;
            $datosRequestTrazabilidad["uuid_actuacion"] = $uuidActuacion;
            $codigo_estado_actuacion = "PENAPR";
            $consultaEstadoActuacion = $this->consultarEstadoActuacion($codigo_estado_actuacion);
            $idEstadoActuacion = $consultaEstadoActuacion[0]->id;
            $datosRequestTrazabilidad["id_estado_actuacion"] = $idEstadoActuacion;
            $datosRequestTrazabilidad["observacion"] = "Actuacion en estado de pendiente de aprobación";
            $datosRequestTrazabilidad["estado"] = true;
            $datosRequestTrazabilidad['created_user'] = auth()->user()->name;
            $datosRequestTrazabilidad['id_dependencia'] = auth()->user()->id_dependencia;

            // Se manda el array del modelo con su informacion para crearlo en su tabla
            $TrazabilidadActuacionesModel = new TrazabilidadActuacionesModel();
            TrazabilidadActuacionesResource::make($TrazabilidadActuacionesModel->create($datosRequestTrazabilidad));

            // Se consultan los datos de la actuacion por el id
            $masActuacionesController = new RepositoryGeneric();
            $masActuacionesController->setModel(new MasActuacionesModel());
            $masActuacionesData = $masActuacionesController->customQuery(function ($model) use ($datosRequest) {
                return
                    $model->where('id', $datosRequest['id_actuacion'])->get();
            });
            $nombreActuacion = $masActuacionesData[0]["nombre_actuacion"];

            // LOG PROCESO DISCIPLINARIO
            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datosRequest['uuid_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] =  LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['descripcion'] = "Se registro la actuación " . $nombreActuacion;
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['id_fase'] = Constants::FASE['actuaciones_evaluacion_pd'];
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = "";
            $logRequest['id_funcionario_registra'] =  $datosRequest['created_user'];
            $logRequest['id_tipo_expediente'] = "";
            $logRequest['id_tipo_sub_expediente'] = "";
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['cierre_etapa'];
            $logRequest['id_fase_registro'] = $array->id;
            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            // Se guarda la ejecucion con un commit para que se ejecute
            DB::connection()->commit();

            // Se retorna la respuesta
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
        return ActuacionesResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ActuacionesFormRequest $request,  $id)
    {
        return ActuacionesResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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


    /**
     * Método que lista las actuaciones donde el uuid del disciplinario y la etapa entren registradas
     *
     */
    public function getActuacionesDisciplinarioEtapa($uuidDisciplinario, $etapa, $estado = 1)
    {
        $query = $this->repository->customQuery(function ($model) use ($uuidDisciplinario, $etapa, $estado) {
            return $model->where('uuid_proceso_disciplinario', $uuidDisciplinario)
                ->where('id_etapa', $etapa)
                ->where('estado', $estado)
                ->whereNull('eliminado')
                ->orWhere('eliminado', '0')
                ->orderBy('created_at', 'desc')
                ->get();
        });

        return ActuacionesCollection::make($query);
    }

    public function getActuacionesDisciplinarioEtapaYDocumentoFinal($uuidDisciplinario, $etapa, $estado)
    {
        //dd(auth()->user());
        $query = $this->repository->customQuery(function ($model) use ($uuidDisciplinario, $etapa, $estado) {
            return $model
                ->select(
                    'actuaciones.uuid',
                    'actuaciones.id_actuacion',
                    'actuaciones.usuario_accion',
                    'actuaciones.id_estado_actuacion',
                    'actuaciones.documento_ruta',
                    'actuaciones.created_user',
                    'actuaciones.updated_user',
                    'actuaciones.deleted_user',
                    'actuaciones.created_at',
                    'actuaciones.updated_at',
                    'actuaciones.estado',
                    'actuaciones.uuid_proceso_disciplinario',
                    'actuaciones.id_etapa',
                    'actuaciones.id_dependencia',
                    'actuaciones.auto',
                    'actuaciones.campos_finales',
                    'actuaciones.eliminado',
                    'actuaciones.id_etapa_siguiente',
                    'actuaciones.id_estado_visibilidad',
                    'actuaciones.id_dependencia_origen',
                    'actuaciones.id_usuario',
                    'actuaciones.incluir_reporte',
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.email_verified_at',
                    'users.password',
                    'users.remember_token',
                    'users.objectguid',
                    'users.nombre',
                    'users.apellido',
                    'users.reparto_habilitado',
                    'users.numero_casos',
                    'users.nivelacion',
                    'users.firma_mecanica',
                    'users.password_firma_mecanica',
                    'users.id_mas_grupo_trabajo_secretaria_comun'
                )
                ->leftJoin('users', 'users.name', '=', 'actuaciones.created_user')
                ->where('actuaciones.uuid_proceso_disciplinario', '=', $uuidDisciplinario)
                ->where('actuaciones.id_etapa', '=', $etapa)
                ->where('actuaciones.estado', '=', $estado)
                ->where('actuaciones.id_estado_visibilidad', '<>', Constants::ESTADOS_VISIBILIDAD['oculto_todos'])
                ->where(function ($query) {
                    $query
                        ->where('actuaciones.id_estado_visibilidad', '=', Constants::ESTADOS_VISIBILIDAD['visible_todos'])
                        ->orWhere('actuaciones.id_estado_visibilidad', '=', Constants::ESTADOS_VISIBILIDAD['oculto_todos'])
                        ->orWhere(function ($query) {
                            $query->where('actuaciones.id_estado_visibilidad', '=', Constants::ESTADOS_VISIBILIDAD['visible_dependencia'])
                                ->where('actuaciones.id_dependencia_origen', '=', auth()->user()->id_dependencia);
                        })
                        ->orWhere(function ($query) {
                            $query->where('actuaciones.id_estado_visibilidad', '=', Constants::ESTADOS_VISIBILIDAD['visible_para_mi_y_jefe'])
                                ->where('actuaciones.id_dependencia_origen', '=', auth()->user()->id_dependencia)
                                ->where(function ($query) {
                                    $query->where('actuaciones.created_user', '=', auth()->user()->name)
                                        ->orWhere('actuaciones.id_dependencia_origen', '=', function ($subQuery) {
                                            $subQuery->select('id')
                                                ->from('mas_dependencia_origen')
                                                ->where('id_usuario_jefe', '=', auth()->user()->id);
                                        });
                                });
                        });
                })
                ->whereNull('eliminado')
                ->orWhere('eliminado', '0')
                ->orderBy('actuaciones.created_at', 'desc')
                ->get();
        });

        //dd($query);

        $arr = array();
        $this->repository->setModel(new ArchivoActuacionesModel());

        $masActuacionesController = new RepositoryGeneric();
        $masActuacionesController->setModel(new TrazabilidadActuacionesModel());

        foreach (ActuacionesCollection::make($query) as $key => $value) {

            $uuidActuaciones = ActuacionesCollection::make($query)[$key]->uuid;

            $query2 = $this->repository->customQuery(function ($model) use ($uuidActuaciones) {
                return $model
                    ->where('uuid_actuacion', $uuidActuaciones)
                    ->where('id_tipo_archivo', 2)
                    ->get();
            });

            // Se trae el usuario que ejecuto la aprobacion o rechazo
            $query3 = $masActuacionesController->customQuery(function ($model) use ($uuidActuaciones) {
                return $model
                    ->where('uuid_actuacion', $uuidActuaciones)
                    ->whereIn('id_estado_actuacion', [1, 2])
                    ->get();
            });

            array_push(
                $arr,
                array(
                    "type" => "buscador",
                    "attributes" => array(
                        "actuacion" => ActuacionesCollection::make($query)[$key],
                        "ArchivoFinalPdf" => ArchivoActuacionesCollection::make($query2)->first(),
                        "UsuarioAprobacion" => TrazabilidadActuacionesCollection::make($query3)->first(),
                    )
                )
            );
        }

        $rtaFinal = array(
            "data" => $arr
        );

        // error_log("rtaFinal -> " . json_encode($rtaFinal));

        //dd("ERROR");

        return json_encode($rtaFinal);
    }

    public function getActuacionesDisciplinarioYDocumentoFinal($uuidDisciplinario, $estado)
    {
        $query = $this->repository->customQuery(function ($model) use ($uuidDisciplinario, $estado) {
            return $model->where('uuid_proceso_disciplinario', $uuidDisciplinario)
                ->where('estado', $estado)
                ->where('id_estado_actuacion', Constants::ESTADOS_ACTUACION['aprobada y pdf definitivo'])
                ->whereNull('eliminado')
                ->orWhere('eliminado', '0')
                ->orderBy('created_at', 'desc')
                ->get();
        });

        $arr = array();
        $this->repository->setModel(new ArchivoActuacionesModel());

        $masActuacionesController = new RepositoryGeneric();
        $masActuacionesController->setModel(new TrazabilidadActuacionesModel());

        foreach (ActuacionesCollection::make($query) as $key => $value) {

            $uuidActuaciones = ActuacionesCollection::make($query)[$key]->uuid;

            $query2 = $this->repository->customQuery(function ($model) use ($uuidActuaciones) {
                return $model
                    ->where('uuid_actuacion', $uuidActuaciones)
                    ->where('id_tipo_archivo', 2)
                    ->get();
            });

            // Se trae el usuario que ejecuto la aprobacion o rechazo
            $query3 = $masActuacionesController->customQuery(function ($model) use ($uuidActuaciones) {
                return $model
                    ->where('uuid_actuacion', $uuidActuaciones)
                    ->whereIn('id_estado_actuacion', [1, 2])
                    ->get();
            });

            array_push(
                $arr,
                array(
                    "type" => "buscador",
                    "attributes" => array(
                        "actuacion" => ActuacionesCollection::make($query)[$key],
                        "ArchivoFinalPdf" => ArchivoActuacionesCollection::make($query2)->first(),
                        "UsuarioAprobacion" => TrazabilidadActuacionesCollection::make($query3)->first(),
                    )
                )
            );
        }

        $rtaFinal = array(
            "data" => $arr
        );

        // error_log("rtaFinal -> " . json_encode($rtaFinal));

        return json_encode($rtaFinal);
    }

    /**
     * Método que lista las actuaciones del sistema creadas y ordenadas por fecha de creacion
     *
     */
    public function getAllActuaciones(Request $request)
    {
        $results = DB::select(DB::raw("SELECT a.uuid, a.id_actuacion, a.usuario_accion, ma.nombre_actuacion, a.id_estado_actuacion, mea.nombre as nombre_estado_actuacion, a.documento_ruta,
        a.created_user
        FROM actuaciones a
        INNER JOIN mas_actuaciones ma on ma.id = a.id_actuacion
        INNER JOIN mas_estado_actuaciones mea on mea.id = a.id_estado_actuacion
        ORDER BY a.created_at asc"));
    }

    /**
     * Método que consulta el id del tipo de archivo de las actuaciones por codigo
     *
     */
    public function consultarIdTipoArchivo($params)
    {
        $results = DB::select(DB::raw("select id from mas_tipo_archivo_actuaciones where codigo = '$params'"));
        return json_decode(json_encode($results));
    }

    /**
     * Método que consulta el id del estado de la actuacion por el codigo
     *
     */
    public function consultarEstadoActuacion($params)
    {
        $results = DB::select(DB::raw("select id, descripcion from mas_estado_actuaciones where codigo = '$params'"));
        return json_decode(json_encode($results));
    }

    /**
     * Método que busca la actuacion por el id
     *
     */
    public function getDatosActuacion($id)
    {
        $query = $this->repository->customQuery(function ($model) use ($id) {
            return $model->where('id', $id)
                ->get();
        });
        return ActuacionesCollection::make($query);
    }

    /**
     * Método que busca todas las actuaciones cque estan dentro del proceso disciplinario y su estado es activo
     *
     */
    public function getActuacionesEstadoActivo($status = 1, $uuid_proceso_disciplinario)
    {
        // Se consultan las actuaciones que estan dentro del proceso disciplinario y su estado es activo
        $query = $this->repository->customQuery(function ($model) use ($status, $uuid_proceso_disciplinario) {
            return $model->where('estado', $status)
                ->where("uuid_proceso_disciplinario", $uuid_proceso_disciplinario)
                ->get();
        });

        // Se inicializa el array
        $arr = [];

        // Se setea el nuevo modelo de la trazabilidad
        $trazabilidadActuacionesController = new RepositoryGeneric();
        $trazabilidadActuacionesController->setModel(new TrazabilidadActuacionesModel());

        // Se Recorre
        foreach (ActuacionesCollection::make($query) as $key => $value) {

            // Se captura el uuid de la actuacion
            $uuidActuacion = ActuacionesCollection::make($query)[$key]->uuid;

            // Se trae el usuario que ejecuto la aprobacion o rechazo
            $queryTrazabilidad = $trazabilidadActuacionesController->customQuery(function ($model) use ($uuidActuacion) {
                return $model->where('uuid_actuacion', $uuidActuacion)
                    ->whereIn('id_estado_actuacion', [1, 2])
                    ->orderBy("created_at", "desc")
                    ->get();
            });

            // Se añade la data en el array
            array_push(
                $arr,
                [
                    "actuacion" => ActuacionesCollection::make($query)[$key],
                    "detalleAprobacion" => TrazabilidadActuacionesCollection::make($queryTrazabilidad)->first(),
                ]
            );
        }

        // Se setea el valor el array principal de data
        $rtaFinal = [
            "data" => $arr
        ];

        // Se retorna la informacion
        return json_encode($rtaFinal);
    }

    /**
     * Método que inactiva las actuaciones seleccionadas
     *
     */
    public function actuacionesInactivar(ActuacionesFormRequest $request)
    {
        // Se inicializa la conexion
        DB::connection()->beginTransaction();

        // Se capturan los datos
        $datosRequest = $request->validated()["data"]["attributes"];;

        // Se captura la informacion a actualizar
        $data = $datosRequest["data"];
        $actualizo = 0;

        // Se recorre el array de las actuaciones
        foreach ($data as $key => $value) {

            // Se captura el uuid de las actuaciones a inactivar
            $uuidActuacion = $value["actuacion"]["id"];

            // Se inactiva el estado de las actuaciones seleccionadas
            $actualizo = ActuacionesModel::where('UUID', $uuidActuacion)->update(['estado' => 0]);

            // Se crea los datos para la tabla de trazabilidad de las actuaciones
            $datosRequestTrazabilidad["uuid_actuacion"] = $uuidActuacion;
            $codigo_estado_actuacion = "ACTINACT";
            $consultaEstadoActuacion = $this->consultarEstadoActuacion($codigo_estado_actuacion);
            $idEstadoActuacion = $consultaEstadoActuacion[0]->id;
            $datosRequestTrazabilidad["id_estado_actuacion"] = $idEstadoActuacion;
            $datosRequestTrazabilidad["observacion"] = $consultaEstadoActuacion[0]->descripcion;
            $datosRequestTrazabilidad["estado"] = true;
            $datosRequestTrazabilidad['created_user'] = auth()->user()->name;
            $datosRequestTrazabilidad['id_dependencia'] = auth()->user()->id_dependencia;

            // Se manda el array del modelo con su informacion para crearlo en su tabla
            $TrazabilidadActuacionesModel = new TrazabilidadActuacionesModel();
            TrazabilidadActuacionesResource::make($TrazabilidadActuacionesModel->create($datosRequestTrazabilidad));

            // Se guarda la ejecucion con un commit para que se ejecute
            DB::connection()->commit();
        }

        // Se retorna el valor
        return ["OK" => $actualizo];
    }


    public function agregarUsuarioParaFirmaMecanica(AgregarUsuarioFirmaMecanicaFormRequest $request)
    {

        try {

            // Se inicializa la conexion
            DB::connection()->beginTransaction();

            // Se capturan los datos
            $datosRequest = $request->validated()["data"]["attributes"];;

            $this->repository->setModel(new FirmaActuacionesModel());

            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model
                    ->where('id_actuacion', $datosRequest["id_actuacion"])
                    ->where('id_user', $datosRequest["id_user"])
                    ->where('tipo_firma', $datosRequest["tipo_firma"])
                    ->whereNull('eliminado')
                    ->orWhere('eliminado', '0')
                    ->get();
            });

            $ExisteEliminado = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model
                    ->where('id_actuacion', $datosRequest["id_actuacion"])
                    ->where('id_user', $datosRequest["id_user"])
                    ->where('tipo_firma', $datosRequest["tipo_firma"])
                    ->where('estado', Constants::ESTADO_FIRMA_MECANICA['Eliminado'])
                    ->whereNull('eliminado')
                    ->orWhere('eliminado', '0')
                    ->get();
            });

            // error_log(count($ExisteEliminado));


            if (count($query) >= 1 && count($ExisteEliminado) < 1) {
                $respuesta = "El usuario ya se encuentra pendiente de firma en esta actuación";
            } else {
                $datosRequest["estado"] = 1;
                $firmaModel = new FirmaActuacionesModel();
                $respuesta = FirmaActuacionesResource::make($firmaModel->create($datosRequest));

                try {
                    $DatosProcesoDisciplinario = ProcesoDiciplinarioModel::where('uuid', $datosRequest["uuid_proceso_disciplinario"])->first();
                    $usuario = User::where('id', $datosRequest["id_user"])->first();


                    $correos = $usuario->email;
                    $nombreGet = !empty($usuario->nombre) ? $usuario->nombre . " " : "";
                    $apellidoGet = !empty($usuario->apellido) ? $usuario->apellido : "";
                    $nombre_usuario = $nombreGet . $apellidoGet;
                    $asunto = "SINPROC: (" . $DatosProcesoDisciplinario->radicado . ") - VIGENCIA (" . $DatosProcesoDisciplinario->vigencia . ')';
                    $contenido = "Estas pendiente de firmar un documento";
                    $archivos = null;
                    $correoscc = null;
                    $correosbbc = null;

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
            // Se guarda la ejecucion con un commit para que se ejecute
            DB::connection()->commit();

            // Se retorna la respuesta
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

    public function FirmasPorActuacion($id_actuacion)
    {
        try {
            $this->repository->setModel(new FirmaActuacionesModel());
            $query = $this->repository->customQuery(function ($model) use ($id_actuacion) {
                return $model
                    ->where('id_actuacion', $id_actuacion)
                    ->whereNull('eliminado')
                    ->orWhere('eliminado', '0')
                    ->orderBy('created_at', 'desc')
                    ->get();
            });
            return FirmaActuacionesCollection::make($query);
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

    public function FirmasPorUsuario($id_user)
    {
        try {
            $this->repository->setModel(new FirmaActuacionesModel());
            $query = $this->repository->customQuery(function ($model) use ($id_user) {
                return $model
                    ->where('id_user', $id_user)
                    ->where('estado', "!=", Constants::ESTADO_FIRMA_MECANICA['Eliminado'])
                    ->whereNull('eliminado')
                    ->orWhere('eliminado', '0')
                    ->orderBy('created_at', 'desc')
                    ->get();
            });
            return FirmaActuacionesCollection::make($query);
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
    public function DocumentosPendientesDeFirmaPorUsuario($id_user)
    {
        try {
            $this->repository->setModel(new FirmaActuacionesModel());
            $query = $this->repository->customQuery(function ($model) use ($id_user) {
                return $model
                    ->where('id_user', $id_user)
                    ->where('estado', "=", Constants::ESTADO_FIRMA_MECANICA['pendiente_de_firma'])
                    ->whereNull('eliminado')
                    ->orWhere('eliminado', '0')
                    ->orderBy('created_at', 'desc')
                    ->get();
            });
            return DocumentosParaFirmaCollection::make($query);
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


    public function DocumentosPendientesDeFirmaPorUsuarioLimitTen($id_user)
    {
        try {
            $this->repository->setModel(new FirmaActuacionesModel());
            $query = $this->repository->customQuery(function ($model) use ($id_user) {
                return $model
                    ->where('id_user', $id_user)
                    ->where('estado', "=", Constants::ESTADO_FIRMA_MECANICA['pendiente_de_firma'])
                    ->whereNull('eliminado')
                    ->orWhere('eliminado', '0')
                    ->orderBy('created_at', 'desc')
                    ->skip(0)->take(10)
                    ->get();
            });
            return DocumentosParaFirmaCollection::make($query);
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



    public function CambiarEstadoFirma(AgregarUsuarioFirmaMecanicaFormRequest $request, $id)
    {
        try {
            // Se inicializa la conexion
            DB::connection()->beginTransaction();
            $usersPorNotificar = array();

            // Se valida la informacion enviada en la peticion
            $datos = $request->validated()["data"]["attributes"];;

            $DatosDocumento = ActuacionesModel::where('uuid', $datos["id_actuacion"])->first();
            $DatosProcesoDisciplinarioByActuacion = FirmaActuacionesModel::where('id_actuacion', $datos["id_actuacion"])->get();
            $uuid_proceso_disciplinario = $DatosProcesoDisciplinarioByActuacion[0]->uuid_proceso_disciplinario;
            $DatosProcesoDisciplinario = ProcesoDiciplinarioModel::where('uuid', $uuid_proceso_disciplinario)->first();

            foreach ($DatosProcesoDisciplinarioByActuacion as $key => $value) {
                if ($value->estado != Constants::ESTADO_FIRMA_MECANICA['Eliminado']) {
                    array_push($usersPorNotificar, $value->id_user);
                }
            }

            $LimpioUsersPorNotificar = array_unique($usersPorNotificar);

            foreach ($LimpioUsersPorNotificar as $key => $value) {
                $usuario = User::where('id', $value)->first();

                $correos = $usuario->email;
                $nombreGet = !empty($usuario->nombre) ? $usuario->nombre . " " : "";
                $apellidoGet = !empty($usuario->apellido) ? $usuario->apellido : "";
                $nombre_usuario = $nombreGet . $apellidoGet;
                $asunto = "SINPROC: (" . $DatosProcesoDisciplinario->radicado . ") - VIGENCIA (" . $DatosProcesoDisciplinario->vigencia . ')';
                $contenido = "El usuario " . auth()->user()->name . " ha firmado el documento " . substr($DatosDocumento->documento_ruta, 34);
                $archivos = null;
                $correoscc = null;
                $correosbbc = null;

                MailTrait::sendMail(
                    $correos,
                    $nombre_usuario,
                    $asunto,
                    $contenido,
                    $archivos,
                    $correoscc,
                    $correosbbc
                );
            }

            // Se setea el modelo de la firma
            $this->repository->setModel(new FirmaActuacionesModel());

            // Se consulta la informacion del tipo de firma
            //dd( $datos["tipo_firma"]);
            $informacionTipoFirma = TipoFirmaModel::where('id', $datos["tipo_firma"])->get();
            $nombreTipoFirma = isset($informacionTipoFirma[0]["nombre"]) ? $informacionTipoFirma[0]["nombre"] : "";
            $tamañoFirmado = isset($informacionTipoFirma[0]["tamano"]) ? $informacionTipoFirma[0]["tamano"] : "";

            // Se consulta la informacion del usuario
            $informacionUsuario = User::where('id', $datos["id_user"])->get();
            $nombreUsuario = isset($informacionUsuario[0]["nombre"]) ? $informacionUsuario[0]["nombre"] . " " . $informacionUsuario[0]["apellido"] : "";
            $dependenciaId = isset($informacionUsuario[0]["id_dependencia"]) ? $informacionUsuario[0]["id_dependencia"] : "";
            $nombreDependencia = "";

            // Se consulta la informacion de la dependencia del usuario
            if (isset($informacionUsuario[0]["id_dependencia"])) {
                $informacionDependencia = DependenciaOrigenModel::where("id", $dependenciaId)->get();
                $nombreDependencia = isset($informacionDependencia[0]["nombre"]) ? $informacionDependencia[0]["nombre"] : "";
            }

            // Se consulta la informacion del estado documento firmado
            $codigoFirmarDocumento = "DOCFIR";
            $informacionFirmaDocumento = MasEstadoActuacionesModel::where("codigo", $codigoFirmarDocumento)->get();
            $nombreFirmaDocumento = isset($informacionFirmaDocumento[0]["nombre"]) ? $informacionFirmaDocumento[0]["nombre"] : "";
            $descripcionFirmaDocumento = isset($informacionFirmaDocumento[0]["descripcion"]) ? $informacionFirmaDocumento[0]["descripcion"] : "";
            $idFirmaDocumento = isset($informacionFirmaDocumento[0]["id"]) ? $informacionFirmaDocumento[0]["id"] : "";

            // Se consulta la informacion del archivo de las actuaciones
            $informacionArchivoActuaciones = ArchivoActuacionesModel::where('uuid_actuacion', $datos["id_actuacion"])->get();
            $nombreArchivo = isset($informacionArchivoActuaciones[0]["nombre_archivo"]) ? $informacionArchivoActuaciones[0]["nombre_archivo"] : "";

            // Se inicializa el array a enviar al generado de la tabla
            $data = [
                "id_actuacion" => $datos["id_actuacion"],
                "nombre_documento" => $datos["nombre_documento"],
                "ruta_image" => $datos["ruta_image"],
                "nombreFirmado" => $nombreTipoFirma,
                "tamanoFirmado" => $tamañoFirmado,
                "nombreUsuario" => $nombreUsuario,
                "dependenciaUsuario" => $nombreDependencia,
                "estadoFirmado" => $nombreFirmaDocumento,
                "descripcionFirmado" => $descripcionFirmaDocumento,
                "idFirmaDocumento" => $idFirmaDocumento,
                "nombreArchivo" => $nombreArchivo,
            ];

            // Se inicializa el controlador del documento
            $return = $this->wordService->wordDocImages($data);

            // Se valida cuando existe un error
            if (isset($return["error"])) {

                // Se retorna el error al frontend
                return $return;
            }

            // Se inserta en la tabla
            $respuesta = FirmaActuacionesResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));

            // Se ejecuta las sentencias
            DB::connection()->commit();

            // Se retorna la respuesta
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

    public function EliminarFirmaMecanicaDeActuacion(AgregarUsuarioFirmaMecanicaFormRequest $request, $id)
    {
        try {
            // Se inicializa la conexion
            DB::connection()->beginTransaction();

            $this->repository->setModel(new FirmaActuacionesModel());

            $respuesta = FirmaActuacionesResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
            DB::connection()->commit();

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
     * Metodo encargado de actualizar el campo CAMPOS_FINALES
     * de la tabla actuaciones
     */
    public function actualizarCamposFinales(Request $request, $id)
    {
        // Se inicializa la variable
        $datos["campos_finales"] = json_encode($request["data"]["attributes"]["campos_finales"]);

        // Se inicializa la conexion
        DB::connection()->beginTransaction();

        // Se actualiza el campo
        $respuesta = ActuacionesResource::make($this->repository->update($datos, $id));

        // Se cierra y ejecuta la sentencia
        DB::connection()->commit();

        // Se retorna la respuesta
        return $respuesta;
    }

    public function UsuarioComisionado($id_proceso_disciplinario)
    {
        try {
            $procesoDisciplinario = new RepositoryGeneric();
            $procesoDisciplinario->setModel(new ProcesoDiciplinarioModel());
            $queryProcesoDisciplinario = $procesoDisciplinario->customQuery(function ($model) use ($id_proceso_disciplinario) {
                return $model
                    ->where('uuid', $id_proceso_disciplinario)
                    ->where('estado', Constants::ESTADOS['activo'])
                    ->get();
            })->first();

            $usuario = new RepositoryGeneric();
            $usuario->setModel(new User());
            $queryUsuario = $usuario->customQuery(function ($model) use ($queryProcesoDisciplinario) {
                return $model
                    ->where('id', $queryProcesoDisciplinario->usuario_comisionado)
                    ->where('estado', Constants::ESTADOS['activo'])
                    ->get();
            })->first();

            if (empty($queryUsuario)) {
                return [
                    'error' =>  "No se encuentra un usuario comisionado"
                ];
            }

            return UsuarioResource::make($queryUsuario);
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

    public function ExistenActuacionesEtapas($uuidDisciplinario)
    {
        try {
            $etapasActuaciones = [
                Constants::ETAPA['evaluacion_pd'],
                Constants::ETAPA['investigacion_preliminar'],
                Constants::ETAPA['investigacion_disciplinaria'],
                Constants::ETAPA['causa_juzgamiento'],
                Constants::ETAPA['proceso_verbal'],
                Constants::ETAPA['segunda_instancia']
            ];
            foreach ($etapasActuaciones as $key => $value) {

                $query = $this->repository->customQuery(function ($model) use ($uuidDisciplinario, $value) {
                    return $model->where('uuid_proceso_disciplinario', $uuidDisciplinario)
                        ->where('id_etapa', $value)
                        ->where('estado', Constants::ESTADOS['activo'])
                        ->whereNull('eliminado')
                        ->orWhere('eliminado', '0')
                        ->orderBy('created_at', 'desc')
                        ->get();
                });

                if (count($query) >= 1) {
                    if ($value == Constants::ETAPA['evaluacion_pd']) {
                        $etapaConActuaciones['evaluacion_pd'] = true;
                    } else if ($value == Constants::ETAPA['investigacion_preliminar']) {
                        $etapaConActuaciones['investigacion_preliminar'] = true;
                    } else if ($value == Constants::ETAPA['investigacion_disciplinaria']) {
                        $etapaConActuaciones['investigacion_disciplinaria'] = true;
                    } else if ($value == Constants::ETAPA['causa_juzgamiento']) {
                        $etapaConActuaciones['causa_juzgamiento'] = true;
                    } else if ($value == Constants::ETAPA['proceso_verbal']) {
                        $etapaConActuaciones['proceso_verbal'] = true;
                    } else if ($value == Constants::ETAPA['segunda_instancia']) {
                        $etapaConActuaciones['segunda_instancia'] = true;
                    }
                } else {
                    if ($value == Constants::ETAPA['evaluacion_pd']) {
                        $etapaConActuaciones['evaluacion_pd'] = false;
                    } else if ($value == Constants::ETAPA['investigacion_preliminar']) {
                        $etapaConActuaciones['investigacion_preliminar'] = false;
                    } else if ($value == Constants::ETAPA['investigacion_disciplinaria']) {
                        $etapaConActuaciones['investigacion_disciplinaria'] = false;
                    } else if ($value == Constants::ETAPA['causa_juzgamiento']) {
                        $etapaConActuaciones['causa_juzgamiento'] = false;
                    } else if ($value == Constants::ETAPA['proceso_verbal']) {
                        $etapaConActuaciones['proceso_verbal'] = false;
                    } else if ($value == Constants::ETAPA['segunda_instancia']) {
                        $etapaConActuaciones['segunda_instancia'] = false;
                    }
                }
            }

            $json['data']['attributes'] = $etapaConActuaciones;
            return json_encode($json);
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

    private function ConsultarActuacionesMigradas($uuidDisciplinario, $actuacionActual)
    {
        $procesoDisciplinario = ProcesoDiciplinarioModel::where('uuid', $uuidDisciplinario)->get();

        if (count($procesoDisciplinario) <= 0) {
            return false;
        }

        $actuaciones_migradas = ActuacionesMigradasModel::where('radicado', $procesoDisciplinario[0]->radicado)
            ->where('vigencia', $procesoDisciplinario[0]->vigencia)
            ->leftJoin('mas_actuaciones ma', 'ma.id', '=', 'actuaciones_migradas.id_tipo_actuacion')
            ->get();

        if (count($actuacionActual) > 0) {
            return false;
        }

        if (count($actuaciones_migradas) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function MostrarIniciarProceso($uuidDisciplinario)
    {
        try {

            $MostrarIniciarProceso["mostrar_boton"] = false;
            $MostrarIniciarProceso["queja_interna"] = false;

            $expediente = DB::select(
                "
                    SELECT
                        a.id_actuacion,
                        a.id_estado_actuacion,
                        a.estado
                    FROM
                        actuaciones a
                    LEFT JOIN mas_actuaciones ma ON a.id_actuacion = ma.id
                    WHERE a.uuid_proceso_disciplinario = '$uuidDisciplinario'
                    AND ma.tipo_actuacion = 1
                    ORDER BY a.created_at DESC
                "
            );

            if ($this->ConsultarActuacionesMigradas($uuidDisciplinario, $expediente)) {
                $MostrarIniciarProceso["mostrar_boton"] = false;
                $MostrarIniciarProceso["queja_interna"] = false;
                $json['data']['attributes'] = $MostrarIniciarProceso;
                return json_encode($json);
            }

            if (count($expediente) > 0) {
                if ($expediente[0]->id_estado_actuacion == 2) {
                    $MostrarIniciarProceso["mostrar_boton"] = true;
                    $MostrarIniciarProceso["queja_interna"] = false;
                    $json['data']['attributes'] = $MostrarIniciarProceso;
                    return json_encode($json);
                }
            }

            //PREGUNTA SI NO ES QUEJA INTERNA
            $expediente = DB::select("
                SELECT
                    id_tipo_queja
                FROM
                    clasificacion_radicado
                WHERE id_proceso_disciplinario = '" . $uuidDisciplinario . "'
                ORDER BY id_etapa DESC
            ");


            if (count($expediente) > 0) {
                if ($expediente[0]->id_tipo_queja ==  Constants::TIPO_QUEJA['interna']) {
                    $MostrarIniciarProceso["mostrar_boton"] = false;
                    $MostrarIniciarProceso["queja_interna"] = true;
                    $json['data']['attributes'] = $MostrarIniciarProceso;
                    return json_encode($json);
                }
            }

            $queryUsuario = DB::select(
                "
                SELECT
                    us.id,
                    lpd.id_dependencia_origen
                FROM proceso_disciplinario
                JOIN log_proceso_disciplinario lpd ON proceso_disciplinario.uuid = lpd.id_proceso_disciplinario
                JOIN mas_dependencia_origen mdo ON mdo.id = lpd.id_dependencia_origen
                JOIN users us ON us.id = mdo.id_usuario_jefe
                WHERE proceso_disciplinario.uuid = '$uuidDisciplinario'
                AND us.estado = " . Constants::ESTADOS['activo'] . "
                ORDER BY lpd.created_at DESC
                "
            );

            //dd($queryUsuario);

            // valido que exista el jefe para seguir con validaciones si no directamente retorno las repuesta en false
            if ($queryUsuario != null) {
                error_log(auth()->user()->id . " == " . $queryUsuario[0]->id);
                // valido que el usuario logeado sea el mismo usuario dueño del proceso si no directamente retorno la respuestas en false
                // dd(auth()->user()->id, $queryUsuario->id);
                //dd(auth()->user()->id, $queryUsuario[0]->id, auth()->user()->id_dependencia, $queryUsuario[0]->id_dependencia_origen);
                if (auth()->user()->id == $queryUsuario[0]->id || auth()->user()->id_dependencia == $queryUsuario[0]->id_dependencia_origen) {

                    $queryActuaciones = DB::table('proceso_disciplinario AS pd')
                        ->leftJoin('actuaciones AS a', 'pd.uuid', '=', 'a.uuid_proceso_disciplinario')
                        ->select('a.uuid', 'a.id_etapa AS id_etapa_actuacion', 'pd.id_etapa AS id_etapa_proceso_disciplinario', 'a.created_user')
                        ->where('a.id_etapa', function ($subquery) use ($uuidDisciplinario) {
                            $subquery->select('id_etapa')
                                ->from('actuaciones')
                                ->where('uuid_proceso_disciplinario', $uuidDisciplinario)
                                ->orderBy('created_at', 'DESC')
                                ->limit(1);
                        })
                        ->where('pd.uuid', $uuidDisciplinario)
                        ->orderBy('a.created_at', 'DESC')
                        ->get();

                    //dd($queryActuaciones);

                    //dd($queryActuaciones[0]->id_etapa_actuacion, $queryActuaciones[0]->id_etapa_proceso_disciplinario, $queryActuaciones[0]->created_user, auth()->user()->name, (count($queryUsuario) > 1 ? $queryUsuario[1]->id_dependencia_origen : $queryUsuario[0]->id_dependencia_origen), auth()->user()->id_dependencia);

                    if (count($queryActuaciones) > 0) {
                        if (
                            $queryActuaciones[0]->id_etapa_actuacion == $queryActuaciones[0]->id_etapa_proceso_disciplinario &&
                            ($queryActuaciones[0]->created_user == auth()->user()->name ||
                                auth()->user()->id_dependencia == (count($queryUsuario) > 1 ? $queryUsuario[1]->id_dependencia_origen : $queryUsuario[0]->id_dependencia_origen)
                            )
                        ) {
                            $MostrarIniciarProceso["mostrar_boton"] = false;
                            $json['data']['attributes'] = $MostrarIniciarProceso;
                            return json_encode($json);
                        } else if (
                            $queryActuaciones[0]->id_etapa_actuacion == $queryActuaciones[0]->id_etapa_proceso_disciplinario &&
                            $queryActuaciones[0]->created_user != auth()->user()->name
                        ) {
                            $MostrarIniciarProceso["mostrar_boton"] = true;
                            $json['data']['attributes'] = $MostrarIniciarProceso;
                            return json_encode($json);
                        } else if (
                            $queryActuaciones[0]->id_etapa_actuacion != $queryActuaciones[0]->id_etapa_proceso_disciplinario &&
                            ($queryActuaciones[0]->created_user == auth()->user()->name ||
                                auth()->user()->id_dependencia == (count($queryUsuario) > 1 ? $queryUsuario[1]->id_dependencia_origen : $queryUsuario[0]->id_dependencia_origen)
                            )
                        ) {
                            $MostrarIniciarProceso["mostrar_boton"] = false;
                            $json['data']['attributes'] = $MostrarIniciarProceso;
                            return json_encode($json);
                        } else {
                            $MostrarIniciarProceso["mostrar_boton"] = true;
                            $json['data']['attributes'] = $MostrarIniciarProceso;
                            return json_encode($json);
                        }
                    } else {
                        $MostrarIniciarProceso["mostrar_boton"] = true;
                        $json['data']['attributes'] = $MostrarIniciarProceso;
                        return json_encode($json);
                    }
                } else {
                    $MostrarIniciarProceso["mostrar_boton"] = true;
                    $json['data']['attributes'] = $MostrarIniciarProceso;
                    return json_encode($json);
                }
            } else {
                $json['data']['attributes'] = $MostrarIniciarProceso;
                return response()->json(array(
                    'code'      =>  403,
                    'message'   =>  "No se encontro el usuario dueño(a) del proceso",
                    'MostrarBoton' => $json,
                ), 403);
            }
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

    public function DependenciaConComisorioAprobado($id_dependencia, $id_proceso_disciplinario)
    {
        try {
            /*$query = $this->repository->customQuery(function ($model) use ($id_dependencia, $id_proceso_disciplinario) {
                return $model
                    ->where('id_dependencia', $id_dependencia)
                    ->where('uuid_proceso_disciplinario', $id_proceso_disciplinario)
                    ->whereNull('eliminado')
                    ->orWhere('eliminado', '0')
                    //->where('estado', "!=", "'" . Constants::ESTADOS['activo'] . "'")
                    ->where('estado', Constants::ESTADOS['activo'])
                    ->get();
            });

            dd($query);

            if (count($query) >= 1) {
                $this->repository->setModel(new MasActuacionesModel());
                foreach ($query as $key => $value) {
                    $id = $value->id_actuacion;
                    $query2 = $this->repository->customQuery(function ($model) use ($id) {
                        return $model
                            ->where('id', $id)
                            ->where('estado', "!=", "'" . Constants::ESTADOS['activo'] . "'")
                            ->get();
                    });
                    foreach ($query2 as $key2 => $value2) {
                        if (
                            $value2->tipo_actuacion != 0 &&
                            $value->id_estado_actuacion != 2 &&
                            $value->id_estado_actuacion != 3 &&
                            $value->id_estado_actuacion != 6 &&
                            $value->id_estado_actuacion != 9 &&
                            $value->id_estado_actuacion != 11 &&
                            $value->id_estado_actuacion != 12
                        ) {
                            error_log($value2);
                            return "Si tiene";
                        } else {
                            //dd($value2->tipo_actuacion);
                            return "No tiene";
                        }
                    }
                }*/

            $query = DB::select(
                "
                    SELECT
                        a.uuid,
                        a.id_actuacion,
                        a.id_estado_actuacion
                    FROM
                        actuaciones a
                    LEFT JOIN mas_actuaciones ma On a.id_actuacion = ma.id
                    WHERE a.id_dependencia = $id_dependencia
                    AND a.uuid_proceso_disciplinario = '$id_proceso_disciplinario'
                    AND (a.eliminado IS NULL OR a.eliminado = '0')
                    AND a.estado = 1
                    AND a.id_estado_actuacion IN (3, 6, 9, 11, 12)
                    AND ma.tipo_actuacion != 0
                "
            );

            if (count($query) > 0) {
                return "No tiene"; //HAY UN IMPEDIMENTO O UN COMISNO QUE NO ESTA APROBADO
            } else {
                //return "No existe";
                return "Si tiene"; //CASO DE EXITO, ES DECIR TODO ESTA APROBADO
            }
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

    /*
    FUNCIÓN ENCARGADA DE MODIFICAR LA ETAPA DE UNA ACTUACIÓN
    */
    public function actualizarEtapa($idActuacion, $idEtapa)
    {
        try {
            DB::connection()->beginTransaction();
            ActuacionesModel::where('UUID', $idActuacion)->update(['id_etapa_siguiente' => $idEtapa]);

            $etapa = DB::select(
                "
                    SELECT
                        me.nombre
                    FROM
                    mas_etapa me
                    WHERE me.id = " . $idEtapa
            );

            $observacion = "Se establece la etapa " . $etapa[0]->nombre . " que continuara despues de la aprobación";

            $datosRequestTrazabilidad["uuid_actuacion"] = $idActuacion;
            $datosRequestTrazabilidad["id_estado_actuacion"] = Constants::ESTADOS_ACTUACION['cambio_etapa'];
            $datosRequestTrazabilidad["observacion"] = $observacion;
            $datosRequestTrazabilidad["estado"] = true;
            $datosRequestTrazabilidad['created_user'] = auth()->user()->name;

            // Se manda el array del modelo con su informacion para crearlo en su tabla
            $TrazabilidadActuacionesModel = new TrazabilidadActuacionesModel();
            TrazabilidadActuacionesResource::make($TrazabilidadActuacionesModel->create($datosRequestTrazabilidad));

            DB::connection()->commit();
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

    public function actualizarEstadoVisibilidad($idActuacion, $idEtapa)
    {
        try {

            DB::connection()->beginTransaction();
            $id_origen_dependencia = auth()->user()->id_dependencia;
            $id_usuario = auth()->user()->id;

            $actualizo = ActuacionesModel::where('UUID', $idActuacion)
                ->update([
                    'id_estado_visibilidad' => $idEtapa,
                    'id_dependencia_origen' => $id_origen_dependencia,
                    'id_usuario' => $id_usuario
                ]);
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

    public function actualizarIncluirReporte($idActuacion, $incluir_reporte)
    {
        try {
            DB::connection()->beginTransaction();

            $actualizo = ActuacionesModel::where('UUID', $idActuacion)
                ->update(['incluir_reporte' => $incluir_reporte]);
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

    public function establecerActuacionesInactivas(ActuacionesInactivasFormRequest $request)
    {
        try {

            // Se inicializa la conexion
            DB::connection()->beginTransaction();

            // Se capturan los datos
            $datosRequest = $request->validated()["data"]["attributes"];

            foreach ($datosRequest as $datos) {
                ActuacionInactivaModel::where('id_actuacion', $datos['id_actuacion'])
                    ->where('id_actuacion_principal', $datos['id_actuacion_principal'])
                    ->where('id_proceso_disciplinario', $datos['id_proceso_disciplinario'])
                    ->delete();
                $datos['created_user'] = auth()->user()->name;
                if ($datos['estado_inactivo'] == '1') {
                    ActuacionInactivaModel::create($datos);
                }
            }

            $observacion = "Se modifico la lista de actuaciones a inactivar";

            $datosRequestTrazabilidad["uuid_actuacion"] = $datosRequest[0]['id_actuacion_principal'];
            $datosRequestTrazabilidad["id_estado_actuacion"] = Constants::ESTADOS_ACTUACION['cambio_lista_actuaciones_inactivar'];
            $datosRequestTrazabilidad["observacion"] = $observacion;
            $datosRequestTrazabilidad["estado"] = true;
            $datosRequestTrazabilidad['created_user'] = auth()->user()->name;

            // Se manda el array del modelo con su informacion para crearlo en su tabla
            $TrazabilidadActuacionesModel = new TrazabilidadActuacionesModel();
            $resultadoUuid = $TrazabilidadActuacionesModel->create($datosRequestTrazabilidad);
            TrazabilidadActuacionesResource::make($resultadoUuid);

            foreach ($datosRequest as $datos) {
                $datosRegistro["uuid_trazabilidad_actuaciones"] = $resultadoUuid->uuid;
                $datosRegistro["uuid_actuacion"] = $datos['id_actuacion'];
                $datosRegistro["id_dependencia"] = auth()->user()->id_dependencia;
                $datosRegistro["estado_anulacion_registro"] = $datos['estado_inactivo'];
                $datosRegistro["created_user"] = auth()->user()->name;
                TrazabilidadActuacionesAnuladasModel::create($datosRegistro);
            }

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

    public function obtenerActuacionesInactivas(ActuacionesInactivasFormRequest $request)
    {
        try {
            // Se capturan los datos
            $datosRequest = $request->validated()["data"]["attributes"][0];

            $actuaciones = DB::select(
                "
                    SELECT
                        a.uuid as id_actuacion,
                        '" . $datosRequest['id_actuacion_principal'] . "' AS id_actuacion_principal,
                        '" . $datosRequest['id_proceso_disciplinario'] . "' AS id_proceso_disciplinario,
                        a.id_estado_actuacion,
                        TO_CHAR(a.created_at, 'DD/MM/YYYY HH:MI:SS AM') AS fecha_creacion,
                        ma.nombre_actuacion,
                        u.nombre AS nombre_usuario,
                        u.apellido AS apellido_usuario,
                        me.nombre AS nombre_etapa,
                        COALESCE((SELECT 1 FROM actuaciones_inactivas ai WHERE ai.id_actuacion_principal = '" . $datosRequest['id_actuacion_principal'] . "' AND ai.id_actuacion = a.uuid), 0) AS estado_inactivo,
                        aa.documento_ruta,
                        aa.uuid AS id_archivo,
                        aa.nombre_archivo,
                        aa.extension AS extension_archivo
                    FROM
                        actuaciones a
                    LEFT JOIN users u ON a.created_user = u.name
                    LEFT JOIN mas_actuaciones ma ON a.id_actuacion = ma.id
                    LEFT JOIN mas_etapa me ON a.id_etapa = me.id
                    LEFT JOIN archivo_actuaciones aa ON a.uuid = aa.uuid_actuacion
                    LEFT JOIN mas_tipo_archivo_actuaciones mtaa ON aa.id_tipo_archivo = mtaa.id
                    WHERE a.estado = 1
                    AND (
                        a.id_estado_actuacion = " . Constants::ESTADOS_ACTUACION['aprobada_pdf_definitivo'] . " OR
                        a.id_estado_actuacion = " . Constants::ESTADOS_ACTUACION['solicitud_inactivación_rechazada'] . " OR
                        a.id_estado_actuacion = " . Constants::ESTADOS_ACTUACION['solicitud_activacion_aceptada'] . "
                    )
                    AND ma.tipo_actuacion = " . Constants::TIPO_ACTUACION['actuacion'] . "
                    AND a.uuid_proceso_disciplinario = '" . $datosRequest['id_proceso_disciplinario'] . "'
                    AND mtaa.codigo = '" . Constants::TIPO_ARCHIVO['documento_definitivo'] . "'
                    ORDER BY a.created_at DESC
                "
            );

            $datos["data"] = $actuaciones;

            return json_encode($datos);

            //return ActuacionesInactivasActuacionCollection::make($actuaciones);

        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    public function obtenerActuacionesMigradas($uuidDisciplinario)
    {
        try {

            $procesoDisciplinario = ProcesoDiciplinarioModel::where('uuid', $uuidDisciplinario)->get();

            $respuesta['data'] = [];

            if (count($procesoDisciplinario) <= 0) {
                return json_encode($respuesta);
            }

            $actuaciones_migradas = ActuacionesMigradasModel::where('radicado', $procesoDisciplinario[0]->radicado)
                ->where('vigencia', $procesoDisciplinario[0]->vigencia)
                ->get();

            return ActuacionesMigradasCollection::make($actuaciones_migradas);
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    /**
     * Metodo encargado de obtener todas las actuaciones dada la etapa
     */
    public function obtenerActuacionesPorEtapa($idEtapa)
    {
        try {

            $actuaciones = DB::select(
                "
                SELECT
                    ma.id,
                    ma.nombre_actuacion
                FROM
                mas_actuaciones ma
                INNER JOIN tbint_mas_etapas_mas_actuaciones tmema ON ma.id = tmema.id_mas_actuacion
                WHERE tmema.id_mas_etapa = $idEtapa
                "
            );

            return ActuacionesEtapaCollection::make($actuaciones);
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    /**
     * Metodo encargado de obtener toda la información relacionada a una actuación
     */
    public function obtenerActuacionProcesoDisciplinario($idActuacion)
    {
        try {
            return ActuacionesProcesoDisciplinarioResource::make($this->repository->find($idActuacion));
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }
}
