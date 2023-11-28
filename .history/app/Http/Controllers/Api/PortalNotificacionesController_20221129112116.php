<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortalNotificacionesFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PortalNotificacionesModel;
use App\Repositories\RepositoryGeneric;
use App\Http\Resources\PortalNotificaciones\PortalNotificacionesCollection;
use App\Http\Resources\PortalNotificaciones\PortalNotificacionesResource;
use App\Http\Controllers\Api\DocumentoSiriusController;
use App\Http\Controllers\Traits\SiriusTrait;
use App\Http\Controllers\Traits\MailTrait;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\DatosInteresadoModel;
use App\Models\AntecedenteModel;
use App\Models\PortalDocumentoNotificacionesModel;
use App\Http\Resources\DatosInteresado\DatosInteresadoCollection;
use App\Http\Resources\Antecedente\AntecedenteCollection;
use App\Http\Resources\ProcesoDiciplinario\ProcesoDiciplinarioCollection;
use App\Http\Resources\PortalDocumentoNotificaciones\PortalDocumentoNotificacionesCollection;
use App\Http\Resources\PortalDocumentoNotificaciones\PortalDocumentoNotificacionesResource;
use stdClass;

class PortalNotificacionesController extends Controller
{
    use SiriusTrait;
    use MailTrait;
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new PortalNotificacionesModel());
    }

    /**
     *  Metodo encargado de traer la informacion de las notificaciones
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Se realiza la consulta de las notificaciones
        $informacionPortalNotificaciones = $this->repository->customQuery(function ($model) {
            return $model->orderBy('created_at', 'desc')
                ->get();
        });

        // Se inicializa el array final
        $arr = array();

        // Se setea el modelo de datos del interesado
        $this->repository->setModel(new DatosInteresadoModel());

        // Se inicializa el controlador de los antecedentes
        $antecedentesController = new RepositoryGeneric();
        $antecedentesController->setModel(new AntecedenteModel());

        // Se inicializa el controlador del proceso disciplinario
        $procesoDisciplinario = new RepositoryGeneric();
        $procesoDisciplinario->setModel(new ProcesoDiciplinarioModel());

        // Se inicializa el controlador de los documentos
        $documentosPortalWeb = new RepositoryGeneric();
        $documentosPortalWeb->setModel(new PortalDocumentoNotificacionesModel());

        // Se recorre el modelo del portal de notificaciones
        foreach (PortalNotificacionesCollection::make($informacionPortalNotificaciones) as $key => $value) {

            // Se captura el tipo y numero de documento y uuid del proceso disciplinario
            $uuidProcesoDisciplinario = PortalNotificacionesCollection::make($informacionPortalNotificaciones)[$key]->uuid_proceso_disciplinario;
            $numeroDocumento = PortalNotificacionesCollection::make($informacionPortalNotificaciones)[$key]->numero_documento;
            $tipoDocumento = PortalNotificacionesCollection::make($informacionPortalNotificaciones)[$key]->tipo_documento;
            $uuidPortalNotificaciones = PortalNotificacionesCollection::make($informacionPortalNotificaciones)[$key]->uuid;

            // Se consulta la informacion del interesado
            $informacionInteresado = $this->repository->customQuery(function ($model) use ($numeroDocumento, $tipoDocumento) {
                return $model
                    ->where('numero_documento', $numeroDocumento)
                    ->where('tipo_documento', $tipoDocumento)
                    ->get();
            });

            // Se consulta la informacion del antecedente
            $informacionAntecedente = $antecedentesController->customQuery(function ($model) use ($uuidProcesoDisciplinario) {
                return $model
                    ->where('id_proceso_disciplinario', $uuidProcesoDisciplinario)
                    ->get();
            });

            // Se consulta la informacion del proceso disciplinario
            $informacionProcesoDisciplinario = $procesoDisciplinario->customQuery(function ($model) use ($uuidProcesoDisciplinario) {
                return $model
                    ->where('uuid', $uuidProcesoDisciplinario)
                    ->get();
            });

            // Se consulta la informacion del proceso disciplinario
            $informacionDocumentosPortalWeb = $documentosPortalWeb->customQuery(function ($model) use ($uuidPortalNotificaciones) {
                return $model
                    ->where('uuid_notificaciones', $uuidPortalNotificaciones)
                    ->get();
            });

            // Se añade la informacion al array general
            array_push(
                $arr,
                array(
                    "type" => "buscador",
                    "attributes" => array(
                        "informacionPortalNotificaciones" => PortalNotificacionesCollection::make($informacionPortalNotificaciones)[$key],
                        "informacionDatosInteresados" => DatosInteresadoCollection::make($informacionInteresado)->first(),
                        "informacionAntecedentes" => AntecedenteCollection::make($informacionAntecedente)->first(),
                        "informacionProcesoDisciplinario" => ProcesoDiciplinarioCollection::make($informacionProcesoDisciplinario)->first(),
                        "informacionDocumentosPortalWeb" => PortalDocumentoNotificacionesCollection::make($informacionDocumentosPortalWeb)->first(),
                    )
                )
            );
        }

        // Se añade el array al array final
        $rtaFinal = array(
            "data" => $arr
        );

        // Se retorna el array en json
        return json_encode($rtaFinal);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PortalNotificacionesFormRequest $request)
    {

        // Se capturan los datos
        $datosRequest = $request->validated();
        $numeroDocumentoSirius = isset($datosRequest["numero_radicado_sirius"]) ? $datosRequest["numero_radicado_sirius"] : null;

        // Se inicializa la clase
        $documentoSiriusControllerR = new RepositoryGeneric();
        $documentoSiriusController = new DocumentoSiriusController($documentoSiriusControllerR);
        $documentoSiriusInformacion = [];

        // Se captura el correo
        $correo = $datosRequest["correo"];
        $nombreCompleto = $datosRequest["nombreCompleto"];
        $detalle = $datosRequest["detalle"];

        // Se inicializa la variable
        $rutaArchivo = "";

        // Se captura la fecha
        $date = date("YmdHis");
        // Se valida que haya numero de documento sirius
        if (!empty($numeroDocumentoSirius)) {

            // Se consulta la informacion del documento
            $informacionDocumentoSirius = $documentoSiriusController->getSoporteRadicadoByExpedienteNotificaciones($numeroDocumentoSirius);
            $documentoDTOList = $informacionDocumentoSirius["documentoDTOList"];

            // Se recorre el array
            foreach ($documentoDTOList as $key => $value) {

                // Se captura la informacion
                $tipoPadreAdjunto = $value["tipoPadreAdjunto"];

                // Se valida cuando el documento pdf es 
                if ($tipoPadreAdjunto == "Principal") {

                    // Se añade la informacion del documento
                    array_push($documentoSiriusInformacion, $value);

                    // Se sale del ciclo
                    break;
                }
            }

            // Se captura la informacion del documento
            $idDocumentoSirius = $documentoSiriusInformacion[0]["idDocumento"];
            $versionDocumentoSirius = $documentoSiriusInformacion[0]["versionLabel"];
            $nombreDocumento = $documentoSiriusInformacion[0]["nombreDocumento"];

            // Se consulta la informacion del documento en SIRIUS
            $documento_sirius = SiriusTrait::buscarDocumento($idDocumentoSirius, $versionDocumentoSirius);

            // Se genera la ruta del documento
            // $path = '/data';
            // $pathIncompleta = '/data';
            $path = storage_path() . '/data';
            $pathIncompleta = '/data';

            // Se valida que no exista la ruta
            if (!file_exists($path)) {

                // Se crea la ruta con permisos
                mkdir($path, 0777, true);
            }

            // Se concadena el path con el numero del documento y la version
            $path = $path . '/' . $date . '_' . $nombreDocumento;
            $pathIncompleta = $pathIncompleta . '/' . $date . '_' . $nombreDocumento;

            // Se genera captura el documento en base64
            $b64 = $documento_sirius['base_64'];

            // Se decodifica el documento
            $bin = base64_decode($b64, true);

            // Se envia el documento a la ruta
            file_put_contents($path, $bin);

            // Se redeclara la variable
            $rutaArchivo = $pathIncompleta;
        }

        // Se realiza la busqueda del proceso disciplinario con el radicado
        $procesoDisciplinario = new RepositoryGeneric();
        $procesoDisciplinario->setModel(new ProcesoDiciplinarioModel());
        $queryProcesoDisciplinario = $procesoDisciplinario->customQuery(function ($model) use ($datosRequest) {
            return
                $model->where('RADICADO', $datosRequest['radicado'])
                ->get();
        });

        // Se captura el UUID del proceso disciplinario
        $uuidProcesoDisciplinario = $queryProcesoDisciplinario[0]["uuid"];

        // Se añade al array
        $datosRequest["uuid_proceso_disciplinario"] = $uuidProcesoDisciplinario;

        // Se inicializa la conexion
        DB::connection()->beginTransaction();

        // Se manda el array del modelo con su informacion para crearlo en su tabla
        $respuesta = PortalNotificacionesResource::make($this->repository->create($datosRequest));
        $array = json_decode(json_encode($respuesta));

        // Se envia el correo electronicon con la notificacion al usuario
        try {

            // Se captura la informacion
            $urlApp = "https://disciplinarios2-dev.personeriabogota.gov.co/login";
            $correos = [$correo];
            $nombre_usuario = $nombreCompleto;
            $asunto = "PORTAL WEB - NOTIFICACIONES";
            $contenido = "Has recibido una notificación, por favor ingresa con tus credenciales al siguiente link para ver el mensaje. " . $urlApp;
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

        // Se captura la informacion para insertar el documento en la tabla
        $datosRequestPortalDocumentos["uuid_notificaciones"] = $array->id;
        $datosRequestPortalDocumentos["documento"] = $documentoSiriusInformacion[0]["nombreDocumento"];
        $datosRequestPortalDocumentos["extension"] = $documentoSiriusInformacion[0]["tipoDocumento"];
        $datosRequestPortalDocumentos['tamano'] = $documentoSiriusInformacion[0]["tamano"];
        $datosRequestPortalDocumentos['ruta'] = $rutaArchivo;
        $datosRequestPortalDocumentos['estado'] = true;

        // Se manda el array del modelo con su informacion para crearlo en su tabla
        $PortalDocumentoNotificacionesModel = new PortalDocumentoNotificacionesModel();
        PortalDocumentoNotificacionesResource::make($PortalDocumentoNotificacionesModel->create($datosRequestPortalDocumentos));

        // Se guarda la ejecucion con un commit para que se ejecute
        DB::connection()->commit();

        // Se retorna
        return $respuesta;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return PortalNotificacionesCollection::make($this->repository->find($id));
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
        return PortalNotificacionesResource::make($this->repository->update($request->validated(), $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->noContent();
    }

    /**
     * Metodo encargado de generar el documento de la notificacion 
     */
    public function getDocumentoNotificaciones($uuidNotificacion)
    {

        // Se inicializa un try catch para retornar en caso de error
        try {

            // Se inicializa la consulta
            $resultadoDocumento = PortalDocumentoNotificacionesModel::where('uuid', $uuidNotificacion)->get();

            // Se concadena la ruta del documento
            $path = storage_path() . $resultadoDocumento[0]["ruta"];

            // Se valida que exista el path
            if (!file_exists($path)) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "No es posible descargar el documento";
                return $error;
            }

            // Se convierte a base 64
            $datos["base_64"] = base64_encode(file_get_contents($path));

            // Se inicializa la extension
            $extension = "pdf";

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

            // Se inicializa el tipo de contenido del archivo
            $datos['content_type'] = $type;

            // Se inicializa el nombre del archivo
            $datos['nombre_documento'] = $resultadoDocumento[0]["documento"];

            // Se retorna los valores
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
}
