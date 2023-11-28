<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortalWebDocumentoFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortalController extends Controller
{
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
    public function store(Request $request)
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function obtenerDocumento(PortalWebDocumentoFormRequest $request)
    {
        $datosRequest = $request->validated();
        $uuid = $datosRequest["data"]["attributes"]["uuid"];

        // Se inicializa la consulta
        $resultadoDocumento = DB::select("SELECT PDN.UUID, PDN.UUID_NOTIFICACIONES, PDN.DOCUMENTO, PDN.RUTA
        FROM PORTAL_DOCUMENTO_NOTIFICACIONES PDN
        WHERE PDN.UUID_NOTIFICACIONES = '$uuid'");

        if(count($resultadoDocumento) <= 0){
            // Se retorna el error
            $datos["error"] = '400';
            $datos["msjRespuesta"] = "La ruta es invalida o no existe el archivo.";
            $datos["archivo"] = null;

            return json_encode($datos);
        }

        // Se concadena la ruta del documento
        $path = storage_path() . $resultadoDocumento[0]->ruta;

        // Se valida que exista el path
        if (!file_exists($path)) {

            // Se retorna el error
            $datos["error"] = '400';
            $datos["msjRespuesta"] = "La ruta es invalida o no existe el archivo. " . $path;
            $datos["archivo"] = null;

            return json_encode($datos);
        }

        // Leer el contenido del archivo y codificarlo en base64
        $fileContents = file_get_contents($path);
        $base64 = base64_encode($fileContents);

        // Se retorna archivo
        $datos["error"] = null;
        $datos["msjRespuesta"] = null;
        $datos["archivo"] = $base64;

        return json_encode($datos);
    }

    public function obtenerDocumentoActuaciones(PortalWebDocumentoFormRequest $request)
    {
        $datosRequest = $request->validated();
        $uuid = $datosRequest["data"]["attributes"]["uuid"];

        // Se inicializa la consulta
        $resultadoDocumento = DB::select("SELECT +
            A.UUID,
            AA.DOCUMENTO_RUTA
        FROM 
            ACTUACIONES A
        INNER JOIN ARCHIVO_ACTUACIONES AA ON A.UUID = AA.UUID_ACTUACION
        WHERE A.UUID = '$uuid'
        AND AA.ID_TIPO_ARCHIVO = 2
        ORDER BY A.CREATED_AT DESC");

        if(count($resultadoDocumento) <= 0){
            // Se retorna el error
            $datos["error"] = '400';
            $datos["msjRespuesta"] = "La ruta es invalida o no existe el archivo.";
            $datos["archivo"] = null;

            return json_encode($datos);
        }

        // Se concadena la ruta del documento
        $path = storage_path() . $resultadoDocumento[0]->documento_ruta;

        // Se valida que exista el path
        if (!file_exists($path)) {

            // Se retorna el error
            $datos["error"] = '400';
            $datos["msjRespuesta"] = "La ruta es invalida o no existe el archivo. " . $path;
            $datos["archivo"] = null;

            return json_encode($datos);
        }

        // Leer el contenido del archivo y codificarlo en base64
        $fileContents = file_get_contents($path);
        $base64 = base64_encode($fileContents);

        // Se retorna archivo
        $datos["error"] = null;
        $datos["msjRespuesta"] = null;
        $datos["archivo"] = $base64;

        return json_encode($datos);
    }
}
