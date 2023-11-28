<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MigracionesTrait;
use Illuminate\Http\Request;
use App\Models\BuscadorModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\LogConsultasModel;
use App\Http\Resources\Buscador\BuscadorCollection;
use App\Http\Resources\MisPendientes\MisPendientesCollection;
use App\Http\Resources\Buscador\BuscadorResource;
use App\Http\Resources\LogConsultas\LogConsultasResource;
use App\Repositories\RepositoryGeneric;
use App\Http\Requests\BuscadorFormRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Traits\UtilidadesTrait;
use App\Models\ActuacionesModel;
use App\Models\DatosInteresadoModel;
use App\Models\EvaluacionModel;
use App\Models\LogProcesoDisciplinarioModel;


class BuscadorMigracionController extends Controller
{

    use UtilidadesTrait;
    use MigracionesTrait;
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new BuscadorModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return BuscadorCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return BuscadorResource::make($this->repository->find($id));
    }


    public function buscadorExpediente(BuscadorFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];

        error_log("HOLA MUNDO");
        error_log(json_encode($datosRequest));
    }

    public function buscadorGeneral(BuscadorFormRequest $request)
    {

        // Se capturan los datos
        $datosRequest = $request->validated()["data"]["attributes"];

        // CONSULTAR EN MIGRACION

        $data_migracion['fechaRegistroDesde'] = null;
        $data_migracion['fechaRegistroHasta'] = null;
        $data_migracion['version'] = null;
        $data_migracion['vigencia'] = "";
        $data_migracion['numeroRadicado'] = $datosRequest["n_expediente"];
        $data_migracion['nombreResponsable'] = "";
        $data_migracion['idResponsable'] = "";
        $data_migracion['dependencia'] = "";
        $data_migracion['idDependencia'] = "";
        $data_migracion['tipoInteresado'] = "";

        $rta_migracion = $this->buscarExpedientePorNumeroRadicado($data_migracion);


        $arr = array();

        for ($cont = 0; $cont < count($rta_migracion['objectresponse']); $cont++) {

            if (str_contains($rta_migracion['objectresponse'][$cont]['idRegistro'], 'V3')) {
                $version = "Excel";
            } else {
                $version = $rta_migracion['objectresponse'][$cont]['version'];
            }

            if ($rta_migracion['objectresponse'][$cont]['vigencia'] != 'NA') {

                $rta_detalle_expediente = $this->buscarDetalleExpediente($datosRequest["n_expediente"], $rta_migracion['objectresponse'][$cont]['vigencia']);

                //VALIDAR SI EL PROCESO YA FUE MIGRADO.
                $validar_proceso_disciplinario = DB::select("select radicado from proceso_disciplinario where radicado = '" . $datosRequest["n_expediente"] . "' and vigencia = " . $rta_migracion['objectresponse'][$cont]['vigencia']);

                if (!empty($validar_proceso_disciplinario)) {
                    $observacion =  "El proceso ya fue migrado.";
                } else {
                    $observacion =  "";
                }

                array_push(
                    $arr,
                    array(
                        "type" => "buscador",
                        /*"attributes" => array(
                            "Id" => $rta_migracion['objectresponse'][$cont]['id'],
                            "Numero_expediente" => $rta_migracion['objectresponse'][$cont]['numeroRadicado'],
                            "Version" => $version,
                            "Dependencia" => $rta_migracion['objectresponse'][$cont]['dependencia'],
                            "Etapa_expediente" => $rta_detalle_expediente['objectresponse']['etapaActual'],
                            "Asunto_del_expediente" => $rta_detalle_expediente['objectresponse']['antecedentes'] == null ? null : $rta_detalle_expediente['objectresponse']['antecedentes'][0]['hechos'],
                            "Nombre_interesado" => $rta_detalle_expediente['objectresponse']['interesados'] == null ? null : $rta_detalle_expediente['objectresponse']['interesados'][0]['nombreCompleto'],
                            "Estado_expediente" => $rta_detalle_expediente['objectresponse']['estadoActual'],
                            "migracion" => 1,
                            "observacion" => $observacion,
                            "proceso_disciplinario" => array(
                                "type" => "proceso_disciplinario",
                                "attributes" => array(
                                    "Auto" => $rta_migracion['objectresponse'][$cont]['idRegistro'],
                                    "vigencia" => $rta_migracion['objectresponse'][$cont]['vigencia'],
                                )
                            )
                        )*/
                    )
                );
            } else {

                array_push(
                    $arr,
                    array(
                        "type" => "buscador",
                        "attributes" => array(
                            "Id" => $rta_migracion['objectresponse'][$cont]['id'],
                            "Numero_expediente" => $rta_migracion['objectresponse'][$cont]['numeroRadicado'],
                            "Version" => $version,
                            "Dependencia" => $rta_migracion['objectresponse'][$cont]['dependencia'],
                            "Etapa_expediente" => "",
                            "Asunto_del_expediente" => "",
                            "Nombre_interesado" => "",
                            "Estado_expediente" =>  "",
                            "migracion" => -1,
                            "observacion" => "",
                            "proceso_disciplinario" => array(
                                "type" => "proceso_disciplinario",
                                "attributes" => array(
                                    "Auto" => $rta_migracion['objectresponse'][$cont]['idRegistro'],
                                    "vigencia" => $rta_migracion['objectresponse'][$cont]['vigencia'],
                                )
                            )
                        )
                    )
                );
            }
        }

        $rtaFinal = array(
            "data" => $arr
        );

        // LOG DE CONSULTA
        $logRequest['id_usuario'] = auth()->user()->id;
        $logRequest['id_proceso_disciplinario'] = $datosRequest["procesoDisciplinarioId"];
        $logRequest['filtros'] = json_encode($datosRequest);
        $logRequest['resultados_busqueda'] = count($arr);

        $logModel = new LogConsultasModel();
        LogConsultasResource::make($logModel->create($logRequest));

        // Se guarda la ejecucion con un commit para que se ejecute
        DB::connection()->commit();

        return json_encode($rtaFinal);
    }


    /**
     *
     */
    public function buscadorMigracion()
    {

        error_log("ESTO ES UNA PRUEBA DE MIGRACION");

        $request_expediente['fechaRegistroDesde'] = null;
        $request_expediente['fechaRegistroHasta'] = null;
        $request_expediente['version'] = null;
        $request_expediente['vigencia'] = "";
        $request_expediente['numeroRadicado'] = "308232";
        $request_expediente['nombreResponsable'] = "";
        $request_expediente['idResponsable'] = "";
        $request_expediente['dependencia'] = "";
        $request_expediente['idDependencia'] = "";
        $request_expediente['tipoInteresado'] = "";

        //$request = json_encode($request_expediente);

        //error_log($request);

        return $this->buscarExpediente($request_expediente);
        //return $this->buscarExpedientePorNumeroRadicado("308232");

    }
}
