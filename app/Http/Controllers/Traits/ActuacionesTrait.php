<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\ArchivoActuaciones\ArchivoActuacionesResource;
use App\Http\Resources\TrazabilidadActuaciones\TrazabilidadActuacionesResource;
use App\Models\ActuacionesModel;
use App\Models\ArchivoActuacionesModel;
use App\Models\TrazabilidadActuacionesModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

trait ActuacionesTrait
{

    /**
     * Metodo encargado de consultar las firmas de las actuaciones
     */
    public function firmasActuaciones($id_actuacion)
    {
        $results = DB::select(DB::raw("select estado from firma_actuaciones where id_actuacion = '$id_actuacion'"));
        return json_decode(json_encode($results));
    }

    /**
     * Metodo encargado de consultar los documentos de la actuacion
     */
    public function documentosActuacion($id_actuacion)
    {
        $results = DB::select(DB::raw("select id_tipo_archivo, documento_ruta, peso, nombre_archivo from archivo_actuaciones where uuid_actuacion = '$id_actuacion'"));
        return json_decode(json_encode($results));
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

    /**
     * Metodo encargado de consultar las firmas de las actuaciones
     */
    public function actuacionFirmadaAprobada($id_actuacion)
    {
        // Se realiza la consulta de las firmas de la actuacion
        $firmaActuaciones = $this->firmasActuaciones($id_actuacion);

        // Se realiza la consulta de los documentos de la actuacion
        $documentoActuaciones = $this->documentosActuacion($id_actuacion);

        // Se inicializan la variable en 0
        $pendienteFirma = 0;
        $encontroDocumentoFinal = 0;
        $rutaDocumento = "";
        $pesoDocumento = 0;
        $numeroUsuariosFirma = 0;
        $nombreDocumento = "";

        // Se recorre los valores de las firmas
        foreach ($firmaActuaciones as $key => $v) {

            // Se captura la informacion
            $estadoFirma = $v->estado;

            // Se valida cuando se encuentra un usuario en pendiente de firma
            /*
                1. Pendiente firma
                2. Firmado
                3. Eliminado
            */

            if ($estadoFirma != 3) {
                $numeroUsuariosFirma++;
            }

            if ($estadoFirma == 1) {
                $pendienteFirma = 1;
                break;
            }
        }

        // Se recorre los valores de los documentos
        foreach ($documentoActuaciones as $key => $v) {

            // Se captura la informacion
            $tipoArchivo = $v->id_tipo_archivo;
            $rutaDocumento = $v->documento_ruta;
            $pesoDocumento = $v->peso;
            $nombreDocumento = $v->nombre_archivo;

            // Se valida cuando se encuentre el documento final
            /*
                1. Documento inicial
                2. Documento final
            */
            if ($tipoArchivo == 2) {
                $encontroDocumentoFinal = 1;
                break;
            }
        }

        // Se valida que todos los usuarios hayan firmado o hayan sido eliminado en el documento o el numero de usuarios sea mayor a 0
        if ($pendienteFirma == 0 && $numeroUsuariosFirma > 0) {

            // Se valida que no se encuentre el documento inicial para generar el documento definitivo en pdf
            if ($encontroDocumentoFinal == 0) {

                // Se concadena el path completo al documento
                $pathDocumento = storage_path() . $rutaDocumento;

                // Se envia a la funcion encargada de generar el documento pdf
                $metodoPdf = $this->convertWordToPdf($pathDocumento, $nombreDocumento);

                error_log("metodoPdf -> " . json_encode($metodoPdf));

                // Se valida que haya sido correcto para actualiza en la base de datos
                if ((isset($metodoPdf[0]["estado"]) && $metodoPdf[0]["estado"])) {

                    // Se valida que haya una ruta valida
                    if (isset($metodoPdf[0]->rutaPdf) && !empty($metodoPdf[0]->rutaPdf)) {

                        // Se reedeclara la variable
                        $rutaxD = $metodoPdf[0]->rutaPdf;
                    } else {

                        // Se reedeclara la variable
                        $rutaxD = $metodoPdf[0]["rutaPdf"];
                    }

                    // Se genera la nueva ruta para el pdf
                    $rutaRelativa = $rutaxD;

                    // Se consulta el id del tipo del archivo del documento final
                    $codigo_tipo_archivo = "DOCFIN";
                    $consultaIdTipoArchivo = $this->consultarIdTipoArchivo($codigo_tipo_archivo);
                    $id_tipo_archivo = $consultaIdTipoArchivo[0]->id;

                    // Se inserta el documento en la tabla de archivo actuaciones
                    $datosRequest['uuid_actuacion'] = $id_actuacion;
                    $datosRequest['id_tipo_archivo'] = $id_tipo_archivo;
                    $datosRequest['documento_ruta'] = $rutaRelativa;
                    $datosRequest['created_user'] = auth()->user()->name;
                    $datosRequest['nombre_archivo'] = basename($nombreDocumento, ".docx") . ".pdf";
                    $datosRequest['extension'] = "pdf";
                    $datosRequest['peso'] = $pesoDocumento;

                    // Se manda el array del modelo con su informacion para crearlo en su tabla
                    $ArchivoActuacionesModel = new ArchivoActuacionesModel();
                    $respuesta = ArchivoActuacionesResource::make($ArchivoActuacionesModel->create($datosRequest));
                    $array = json_decode(json_encode($respuesta));

                    // Se crea los datos para la tabla de trazabilidad de las actuaciones
                    $uuidActuacion = $array->attributes->uuid_actuacion;

                    // Se inserta el registro en la trazabilidad de las actuaciones
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
                    //DB::connection()->commit();

                    // Se retorna la respuesta
                    return [
                        "estado" => true
                    ];
                } else {

                    // Se retorna el error
                    return [
                        "estado" => false,
                        "data" => $metodoPdf
                    ];
                }
            }
        } else if ($numeroUsuariosFirma == 0) {

            // Se retorna porque no hay usuarios para firma
            return [
                "estado" => true
            ];
        }

        // Se retorna el error
        return [
            "estado" => false
        ];
    }

    /**
     * Metodo encargado de consumir el servicio encargado de generar el pdf definitivo
     */
    public function convertWordToPdf($rutaDocumento, $nombreDocumento)
    {
        // Ruta del documento
        $ruta = $rutaDocumento;

        // Se inicializa el array de respuesta
        $array = [];

        // Se captura la url del servicio de firma
        $apiURL = env("RUTA_SERVICIO_FIRMA");

        // Se convierte el documento a base 64
        $base64Documento = base64_encode(file_get_contents($ruta));

        // Se concadena el arreglo
        $var = ["base64File" => "$base64Documento"];

        // Se consume el servicio
        $response = Http::withoutVerifying()
            ->withOptions([
                "verify" => false
            ])
            ->withHeaders([
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json'
            ])
            ->send(
                'POST',
                $apiURL,
                ['body' => json_encode($var)]
            );

        // Se captura el codigo de respuesta
        $statusCode = $response->status();
        $mensajeRequest = $response->body();

        // Se captura la fecha
        $año = date("Y");
        $mes = date("m");
        $dia = date("d");
        $hor = date("h");
        $min = date("i");
        $sec = date("s");

        // Se captura el nombre del documento
        $nombreDocumento = basename($nombreDocumento, ".docx");

        // Se valida el codigo de error
        if ($statusCode == "200") {

            // Se valida que exista el archivo convertido en base 64
            if (isset($response['file'])) {

                // Se captura el archivo en base 64
                $bin = base64_decode($response['file'], true);

                // Se inicializa la ruta a guardar el archivo
                $rutaRelativaArchivo = '/data/' . $año . $mes . $dia . $hor . $min . $sec . '_' . $nombreDocumento . ".pdf";
                $rutaCompleta = storage_path() . $rutaRelativaArchivo;

                // Se añade el archivo en la carpeta compartida del servidor
                file_put_contents($rutaCompleta, $bin);

                // Se redeclara el array en estado ok
                array_push(
                    $array,
                    [
                        "estado" => true,
                        "rutaPdf" => $rutaRelativaArchivo,
                        "mensajeService" => $mensajeRequest
                    ]
                );
            }
        } else {

            // Se redeclara el array en estado ok
            array_push(
                $array,
                [
                    "estado" => false,
                    "rutaPdf" => "Ha ocurrido un error al tratar de generar el documento en pdf.",
                    "mensajeService" => $mensajeRequest
                ]
            );
        }

        // Se retorna el resultado
        return $array;
    }
}
