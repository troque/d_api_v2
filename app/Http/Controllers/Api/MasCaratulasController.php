<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasCaratulasModel;
use App\Models\TipoUnidadModel;
use Illuminate\Http\Request;
use App\Repositories\RepositoryGeneric;
use App\Http\Resources\Caratulas\CaratulasCollection;
use App\Http\Resources\Caratulas\CaratulasResource;
use App\Http\Requests\MasCaratulasFormRequest;
use App\Models\EntidadInvestigadoModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\DatosInteresadoModel;
use App\Models\AntecedenteModel;
use App\Services\WordServices;
use DateTime;
use App\Http\Controllers\Api\ParametroCamposCaratulasController;

class MasCaratulasController extends Controller
{
    private $repository;
    private $wordService;
    private $parametroCamposCaratula;

    public function __construct(RepositoryGeneric $repository, WordServices $wordService, ParametroCamposCaratulasController $parametroCamposCaratula)
    {
        $this->repository = $repository;
        $this->repository->setModel(new MasCaratulasModel());
        $this->wordService = $wordService;
        $this->parametroCamposCaratula = $parametroCamposCaratula;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return CaratulasCollection::make($this->repository->paginate($request->limit ?? 100));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return CaratulasResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MasCaratulasFormRequest $request, $id)
    {
        // Se consulta el archivo con el id
        $datosCaratula = $this->repository->find($id);

        // Se genera la ruta folder de la caratula
        $baseFolderPath = storage_path() . '/files/templates/caratulas/';

        // Se captura la data
        $data = $request->validated()["data"]["attributes"];

        // Se valida que tenga un archivo en base 64 para actualizar
        if (empty($data["file_base64"])) {

            // Se quita el valor del nombre de la plantilla
            unset($data["nombre_plantilla"]);

            // Se retorna el update
            return CaratulasResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
        } else {

            // Se captura el base 64 del archivo
            $b64 = $data['file_base64'];

            // Se decodifica el archivo
            $bin = base64_decode($b64, true);

            // Se concadena la fecha
            $date = date("YmdHid");

            // Se concadena la fecha al nombre de la plantilla
            $nombreConcadenado = $date . "_" . $data['nombre_plantilla'];

            // Se concadena la ruta completa del archivo
            $rutaCompleta = $baseFolderPath . $nombreConcadenado;

            // Se coloca el archivo en la ruta
            file_put_contents($rutaCompleta, $bin);

            // Se concadena el archivo anterior a eliminar
            $archivoEliminar = $baseFolderPath . $datosCaratula->nombre_plantilla;

            // Se elimina el archivo anterior
            if (!unlink($archivoEliminar)) {

                return [
                    "error" => "Ocurrio un error al eliminar el archivo anterior de la caratúla"
                ];
            }

            // Nombre de la tabla
            $data["nombre_plantilla"] = $nombreConcadenado;

            // Se retorna
            return CaratulasResource::make($this->repository->update($data, $id));
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
        $this->repository->delete($id);
        return response()->noContent();
    }

    // Metodo encargado de generar los parametros de la plantilla
    public function getParametrosPlantilla($caratulaId)
    {
        // Se consulta la caratula dependiendo el id
        $caratula = $this->repository->find($caratulaId);

        // Se genera la ruta folder de la caratula
        $baseFolderPath = storage_path() . '/files/templates/caratulas/';
        $path = $baseFolderPath . $caratula->nombre_plantilla;

        // Se consulta los parametros de la plantilla
        $result = $this->wordService->get_document_params($path);

        // Se retorna la respuesta en json
        return response()->json($result);
    }

    /**
     * Retorna plantilla diligenciada con valores de parametros enviados en request
     */
    public function getPlantillaDiligenciada($caratulaId, Request $request)
    {

        // Se busca la informacion de la caratula por el Id
        $caratula = $this->repository->find($caratulaId);
        $baseFolderPath = storage_path() . '/files/templates/caratulas/';

        // Se consulta la informacion de los parametros de la caratula del sistema
        $arrayParametrosCaratula = $this->parametroCamposCaratula->consultarParametrosCaratulas();

        // Se captura el nombre y de concadena con el path
        $path = $baseFolderPath . $caratula->nombre_plantilla;

        // Se captura la fecha
        $date = date("Ymdhis");

        // Se capturan los parametros
        $params = $request->input('data.attributes.params');

        // Se envia al metodo encargado de generar el pdf
        $result = $this->wordService->replace_document_params_pdf($path, $params, $arrayParametrosCaratula);

        // Se genera un array con la informacion del pdf en base64
        $datos['file_name'] = $caratula->nombre . $date . ".pdf";
        $datos['content_type'] = "application/pdf";
        $datos["params"] = $params;
        $datos["base_64"] = base64_encode(file_get_contents($result["pdf"]));

        // Se elimina el archivo pdf en tmp
        if (unlink($result["pdf"])) {
            error_log("Se elimino el pdf");
        }

        // Se retorna la respuesta en json
        return response()->json($datos);
    }

    /**
     * Retorna plantilla diligenciada con valores de parametros enviados en request
     */
    public function getCaratulaRamasProceso($uuidProcesoDisciplinario)
    {
        // Se inicializa el array
        $informacionGeneral = [];

        // Se consulta la informacion del expediente
        $informacionGeneralProcesoDisciplinario = ProcesoDiciplinarioModel::where("uuid", $uuidProcesoDisciplinario)->get();
        $informacionEntidadInvestigado = EntidadInvestigadoModel::where("id_proceso_disciplinario", $uuidProcesoDisciplinario)->get();
        $informacionInteresadosInvestigado = DatosInteresadoModel::where("id_proceso_disciplinario", $uuidProcesoDisciplinario)->get();
        $informacionAntecedentes = AntecedenteModel::where("id_proceso_disciplinario", $uuidProcesoDisciplinario)->get();

        // Se añade al array general el resto de informacion
        $informacionGeneral = [
            "informacionGeneralProcesoDisciplinario" => $informacionGeneralProcesoDisciplinario,
            "informacionEntidadInvestigado" => $informacionEntidadInvestigado,
            "informacionInteresadosInvestigado" => $informacionInteresadosInvestigado,
            "informacionAntecedentes" => $informacionAntecedentes,
        ];

        // Se captura la fecha
        $fecha = new DateTime();
        $fecha = $fecha->format("Ymd-His-v");

        // Se procede a generar la caratula
        $result = $this->wordService->generarCaratula($informacionGeneral);
        $guid =  "Caratula_" . $fecha;

        // Generales del documento
        $datos['file_name'] = $guid . ".pdf";
        $datos['content_type'] = "application/msword";
        $datos["base_64"] = base64_encode(file_get_contents($result["pdf"]));

        // Se elimina el archivo temporal de word
        if (unlink($result["pdf"])) {
            error_log("Se elimino el word de caratulas ramas del proceso");
        }

        // Se retorna la respuesta
        return response()->json($datos);
    }

    /**
     * Obtiene archivo en base64
     *
     */
    public function getArchivoCaratula($id)
    {
        // Se consulta el archivo con el id
        $datosCaratula = $this->repository->find($id);

        // Se concadena el folder path
        $baseFolderPath = storage_path() . '/files/templates/caratulas/';

        // Se concadena el path con el nombre del archivo
        $path = $baseFolderPath . $datosCaratula->nombre_plantilla;

        // Se añade en el array el nombre y los datos en base 64
        $datos['file_name'] = $datosCaratula->nombre_plantilla;
        $datos['content_type'] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        $datos["base_64"] = base64_encode(file_get_contents($path));

        // Se retorne en json
        return response()->json($datos);
    }
}
