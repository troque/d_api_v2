<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WordServices;
use Illuminate\Http\Request;

class WordDocController extends Controller
{
    protected $wordService;

    public function __construct(WordServices $wordService)
    {
        $this->wordService = $wordService;
    }

    /**
     * Obtiene lista de parametros de un documento
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $path = storage_path() . '/files/templates/example.docx';
        // $result = $this->wordService->get_document_params($path);

        // return response()->json($result);
    }

    /**
     * Retorna plantilla diligenciada con valores de parametros enviados en request
     */
    public function store(Request $request)
    {
        $path = storage_path() . '/files/Actuaciones/2022/06/16/doc_prueba.docx';
        $params = $request->input('data.attributes.params');
        $result = $this->wordService->replace_document_params($path, $params);

        $datos["base_64"] = base64_encode(file_get_contents($result));
        $datos['content_type'] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        $datos["params"] = $params;
        return response()->json($datos);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($id);
    }

    /**
     * Retorna plantilla diligenciada con valores de parametros enviados en request
     */
    public function wordDocFile(Request $request)
    {
        $path = storage_path() . '/files/Actuaciones/2022/06/16/doc_prueba.docx';
        $params = $request->input('data.attributes.params');
        $result = $this->wordService->replace_document_params($path, $params);

        $año = date("Y");
        $mes = date("d");
        $dia = date("m");
        $h = date("h");
        $i = date("i");
        $s = date("s");

        $datos["base_64"] = base64_encode(file_get_contents($result));
        $datos["ruta_archivo"] = $result;
        $datos['content_type'] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        $datos["params"] = $params;
        $nombreArchivo = 'Archivo_Diligenciado_' . $año . $mes . $dia . $h . $i . $s . '.docx';

        return response()->download($datos["ruta_archivo"], $nombreArchivo);
    }
}
