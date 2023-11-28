<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ArchivoActuacionesModel;
use App\Http\Requests\ArchivoActuacionesFormRequest;
use App\Http\Resources\ArchivoActuaciones\ArchivoActuacionesCollection;
use App\Http\Resources\ArchivoActuaciones\ArchivoActuacionesResource;
use Illuminate\Support\Facades\DB;
use App\Repositories\RepositoryGeneric;
use stdClass;
use App\Models\TrazabilidadActuacionesModel;
use App\Models\ActuacionesModel;
use App\Http\Resources\TrazabilidadActuaciones\TrazabilidadActuacionesResource;
use App\Http\Utilidades\Constants;

class ArchivoActuacionesController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ArchivoActuacionesModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ArchivoActuacionesCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArchivoActuacionesFormRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ArchivoActuacionesResource::make($this->repository->find($id));
    }



    /**
     * Método que lista todas los archivos por actuacion que se han hecho en el sistema
     *
     */
    public function getAllArchivosActuacionesByUuid($uuid_actuacion)
    {
        $query = $this->repository->customQuery(function ($model) use ($uuid_actuacion) {
            return $model->select("archivo_actuaciones.uuid as id", "archivo_actuaciones.uuid_actuacion", "archivo_actuaciones.id_tipo_archivo", "archivo_actuaciones.documento_ruta", "archivo_actuaciones.nombre_archivo", "archivo_actuaciones.extension", "archivo_actuaciones.peso")
                ->join('actuaciones a', 'a.uuid', '=', 'uuid_actuacion')
                ->join('mas_tipo_archivo_actuaciones mta', 'mta.id', '=', 'id_tipo_archivo')
                ->where('uuid_actuacion', $uuid_actuacion)
                ->get();
        });

        return ArchivoActuacionesCollection::make($query);
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

    public function getDocumento($uuid, $extension)
    {
        try {

            $resultadoDocumento = $this->repository->customQuery(function ($model) use ($uuid) {
                return $model
                    ->where('uuid', $uuid)
                    ->get();
            });

            $path = storage_path() . $resultadoDocumento[0]["documento_ruta"];

            if (!file_exists($path)) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "No es posible descargar el documento";
                return $error;
            }

            $datos["base_64"] = base64_encode(file_get_contents($path));

            switch ($extension) {
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
                case 'docx':
                    $type = "content-type: application/msword";
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
        }
    }

    public function upArchivoDefinitivo(ArchivoActuacionesFormRequest $request)
    {
        // Se capturan los datos
        $datosRequest = $request->validated();

        // Se captura la fecha
        $año = date("Y");
        $mes = date("m");
        $dia = date("d");
        $hor = date("h");
        $min = date("i");
        $sec = date("s");
        $actuacionesNombreCarpeta = Constants::ACTUACIONES_NOMBRE_CARPETA;

        // Se valida el archivo
        //$rutaRelativaArchivo = '/files' . '/' . $actuacionesNombreCarpeta . '/' . $año . '/' . $mes . '/' . $dia . '/' . $año . $mes . $dia . $hor . $min . $sec . '_' . $datosRequest['nombre_archivo']; //MODIFICACION ANTERIOR
        $rutaRelativaArchivo = '/files' . '/' . $actuacionesNombreCarpeta . '/' . $año . $mes . $dia . $hor . $min . $sec . '_' . $datosRequest['nombre_archivo'];   //MODIFICACION NUEVA
        $rutaCompleta = storage_path() . $rutaRelativaArchivo;
        //$path = storage_path() . '/files' . '/' . $actuacionesNombreCarpeta . '/' . $año . '/' . $mes . '/' . $dia; //MODIFICACION ANTERIOR
        $path = storage_path() . '/files' . '/' . $actuacionesNombreCarpeta;   //MODIFICACION NUEVA

        // Se valida que si no existe se crea la carpeta
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $b64 = $datosRequest['fileBase64'];
        $bin = base64_decode($b64, true);
        file_put_contents($rutaCompleta, $bin);

        $codigo_tipo_archivo = "DOCFIN";
        $consultaIdTipoArchivo = $this->consultarIdTipoArchivo($codigo_tipo_archivo);
        $id_tipo_archivo = $consultaIdTipoArchivo[0]->id;

        // Campos de la tabla
        $datosRequest['uuid_actuacion'] = $datosRequest['uuid_actuacion'];
        $datosRequest['id_tipo_archivo'] = $id_tipo_archivo;
        $datosRequest['documento_ruta'] = $rutaRelativaArchivo;
        $datosRequest['created_user'] = auth()->user()->name;
        $datosRequest['nombre_archivo'] = $datosRequest['nombre_archivo'];
        $datosRequest['extension'] = $datosRequest['extension'];
        $datosRequest['peso'] = $datosRequest['peso'];

        // Se manda el array del modelo con su informacion para crearlo en su tabla
        $respuesta = ArchivoActuacionesResource::make($this->repository->create($datosRequest));
        $array = json_decode(json_encode($respuesta));

        // Se crea los datos para la tabla de trazabilidad de las actuaciones
        $uuidActuacion = $array->attributes->uuid_actuacion;

        $datosRequestTrazabilidad["uuid_actuacion"] = $uuidActuacion;
        $codigo_estado_actuacion = "PDFDEF";
        $consultaEstadoActuacion = $this->consultarEstadoActuacion($codigo_estado_actuacion);
        $idEstadoActuacion = $consultaEstadoActuacion[0]->id;
        $datosRequestTrazabilidad["id_estado_actuacion"] = $idEstadoActuacion;
        $datosRequestTrazabilidad["observacion"] = "Actuación en estado de cargue del documento definitivo";
        $datosRequestTrazabilidad["estado"] = true;
        $datosRequestTrazabilidad['created_user'] = auth()->user()->name;

        // Se manda el array del modelo con su informacion para crearlo en su tabla
        $TrazabilidadActuacionesModel = new TrazabilidadActuacionesModel();
        TrazabilidadActuacionesResource::make($TrazabilidadActuacionesModel->create($datosRequestTrazabilidad));

        // Se manda el array del modelo con su informacion para crearlo en su tabla
        $respuestaUpdate = ActuacionesModel::where('UUID', $uuidActuacion)->update(['id_estado_actuacion' => $idEstadoActuacion, 'updated_user' => auth()->user()->name]);
        $arrayUpdate = json_decode(json_encode($respuestaUpdate));

        // Se guarda la ejecucion con un commit para que se ejecute
        DB::connection()->commit();

        // Se retorna la respuesta
        return $respuesta;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $uuid
     * @param  String  $uuid_actuacion
     * @return \Illuminate\Http\Response
     */
    public function updateArchivoActuaciones(ArchivoActuacionesFormRequest $request, $uuid, $uuid_actuacion)
    {
        // Se capturan los datos
        $datosRequest = $request->validated();

        // Se valida el archivo
        $año = date("Y");
        $mes = date("m");
        $dia = date("d");
        $hor = date("h");
        $min = date("i");
        $sec = date("s");
        $actuacionesNombreCarpeta = Constants::ACTUACIONES_NOMBRE_CARPETA;
        //$rutaRelativaArchivo = '/files' . '/' . $actuacionesNombreCarpeta . '/' . $año . '/' . $mes . '/' . $dia . '/' . $año . $mes . $dia . $hor . $min . $sec . '_' . $datosRequest['nombre_archivo']; //MODIFICACION ANTERIOR
        $rutaRelativaArchivo = '/files' . '/' . $actuacionesNombreCarpeta . '/' . $año . $mes . $dia . $hor . $min . $sec . '_' . $datosRequest['nombre_archivo']; //MODIFICACION NUEVA
        $rutaCompleta = storage_path() . $rutaRelativaArchivo;
        //$path = storage_path() . '/files' . '/' . $actuacionesNombreCarpeta . '/' . $año . '/' . $mes . '/' . $dia; //MODIFICACION ANTERIOR
        $path = storage_path() . '/files' . '/' . $actuacionesNombreCarpeta;  //MODIFICACION NUEVA

        // Se valida que si no existe se crea la carpeta
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $b64 = $datosRequest['fileBase64'];
        $bin = base64_decode($b64, true);
        file_put_contents($rutaCompleta, $bin);

        // Se manda el array del modelo con su informacion para crearlo en su tabla
        $respuesta = ArchivoActuacionesModel::where('UUID', $uuid)->update(['documento_ruta' => $rutaRelativaArchivo, 'nombre_archivo' => $datosRequest["nombre_archivo"], "extension" => $datosRequest["extension"], "peso" => $datosRequest["peso"]]);
        $array = json_decode(json_encode($respuesta));

        // Se crea el registro en la trazabilidad
        $tipoDocumentoActualizar = $datosRequest["tipoDocumentoActualizar"];
        $msjTipoDocumento = "";

        // Se valida el tipo de documento que se va a actualizar
        if ($tipoDocumentoActualizar == 0) {
            $msjTipoDocumento = "inicial";
        } else if ($tipoDocumentoActualizar == 1) {
            $msjTipoDocumento = "definitivo";
        }

        $datosRequestTrazabilidad["uuid_actuacion"] = $uuid_actuacion;
        $codigo_estado_actuacion = "ACTDOC";
        $consultaEstadoActuacion = $this->consultarEstadoActuacion($codigo_estado_actuacion);
        $idEstadoActuacion = $consultaEstadoActuacion[0]->id;
        $datosRequestTrazabilidad["id_estado_actuacion"] = $idEstadoActuacion;
        $datosRequestTrazabilidad["observacion"] = "Actuación en estado de actualización del documento" . " " . $msjTipoDocumento;
        $datosRequestTrazabilidad["estado"] = true;
        $datosRequestTrazabilidad['created_user'] = auth()->user()->name;

        // Se manda el array del modelo con su informacion para crearlo en su tabla
        $TrazabilidadActuacionesModel = new TrazabilidadActuacionesModel();
        TrazabilidadActuacionesResource::make($TrazabilidadActuacionesModel->create($datosRequestTrazabilidad));

        // Se guarda la ejecucion con un commit para que se ejecute
        DB::connection()->commit();

        return $respuesta;
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
        $results = DB::select(DB::raw("select id from mas_estado_actuaciones where codigo = '$params'"));
        return json_decode(json_encode($results));
    }
}