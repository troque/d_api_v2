<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasActuacionesModel;
use App\Models\TrazabilidadActuacionesModel;
use App\Models\ArchivoActuacionesModel;
use App\Models\ActuacionesModel;
use App\Http\Requests\MasActuacionesFormRequest;
use App\Http\Requests\MasActuacionesUpdateFormRequest;
use App\Http\Resources\MasActuaciones\MasActuacionCollection;
use App\Http\Resources\MasActuaciones\MasActuacionResource;
use Illuminate\Support\Facades\DB;
use App\Repositories\RepositoryGeneric;
use App\Services\WordServices;
use App\Http\Controllers\Api\ArchivoActuacionesController;
use App\Http\Controllers\Traits\ActuacionesTrait;
use App\Http\Controllers\Traits\ParametrosTrait;
use App\Http\Resources\ArchivoActuaciones\ArchivoActuacionesResource;
use App\Http\Resources\TrazabilidadActuaciones\TrazabilidadActuacionesResource;
use App\Models\MasEtapaMasActuacionModel;
use App\Models\Role;
use App\Models\RolMasActuacionModel;
use Illuminate\Support\Facades\Http;

class MasActuacionesController extends Controller
{
    use ActuacionesTrait;
    use ParametrosTrait;
    private $repository;
    private $wordService;
    private $archivoActuaciones;

    public function __construct(RepositoryGeneric $repository, WordServices $wordService, ArchivoActuacionesController $archivoActuaciones)
    {
        $this->repository = $repository;
        $this->repository->setModel(new MasActuacionesModel());
        $this->wordService = $wordService;
        $this->archivoActuaciones = $archivoActuaciones;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return MasActuacionCollection::make($this->repository->paginate($request->limit ?? 1000));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MasActuacionesFormRequest $request)
    {
        try {
            // Se inicializa la conexion
            DB::connection()->beginTransaction();

            // Se capturan los datos
            $datosRequest = $request->validated()["data"]["attributes"];

            // Se inicializa el path donde se almacena
            $baseFolderPath = storage_path() . '/files/templates/actuaciones/';

            // Se concadena el path con el nombre del archivo en la variable
            $path = $baseFolderPath . $datosRequest['nombre_plantilla'];

            // Se captura el id de la etapa
            //$etapaId = $datosRequest["id_etapa"]["value"];
            //$datosRequest["id_etapa"] = $etapaId;

            // Si el archivo existe se cambia nombre de archivo
            if (file_exists($path)) {

                // Se concadena el path con el nombre de la plantilla
                $path = $baseFolderPath . $datosRequest['nombre_plantilla'];
            }

            // Se captura el file en base 64
            $b64 = $datosRequest['fileBase64'];

            // Se codifica el manual en base 64
            $bin = base64_decode($b64, true);

            // Se coloca el archivoe en la ruta
            file_put_contents($path, $bin);

            // Se valida que el manual sea diferente de null
            if ($datosRequest['nombre_plantilla_manual'] != null) {

                // Se concadena el path con el nombre del manual
                $pathManual = $baseFolderPath . $datosRequest['nombre_plantilla_manual'];

                // Si el archivo existe se cambia nombre de archivo
                if (file_exists($pathManual)) {

                    // Se concadena el path con el nombre del manual
                    $pathManual = $baseFolderPath . $datosRequest['nombre_plantilla_manual'];
                }

                // Se captura el nombre del manual
                $b64Manual = $datosRequest['fileBase64_manual'];

                // Se codifica el manual en base 64
                $binManual = base64_decode($b64Manual, true);

                // Se coloca el archivo dentro de la ruta
                file_put_contents($pathManual, $binManual);
            }

            // Se añade un nuevo array de datos
            $datosArray["nombre_actuacion"] = $datosRequest["nombre_actuacion"];
            $datosArray["nombre_plantilla"] = $datosRequest["nombre_plantilla"];
            //$datosArray["id_etapa"] = $datosRequest["id_etapa"];
            $datosArray["estado"] = true;
            //$datosArray["id_etapa_despues_aprobacion"] = $datosRequest["id_etapa_despues_aprobacion"];
            $datosArray["generar_auto"] = $datosRequest["generar_auto"];
            $datosArray["despues_aprobacion_listar_actuacion"] = $datosRequest["despues_aprobacion_listar_actuacion"];
            $datosArray["nombre_plantilla_manual"] = $datosRequest["nombre_plantilla_manual"];
            $datosArray["fileBase64_manual"] = $datosRequest["fileBase64_manual"];
            $datosArray["texto_dejar_en_mis_pendientes"] = $datosRequest["texto_dejar_en_mis_pendientes"];
            $datosArray["texto_enviar_a_alguien_de_mi_dependencia"] = $datosRequest["texto_enviar_a_alguien_de_mi_dependencia"];
            $datosArray["texto_enviar_a_jefe_de_la_dependencia"] = $datosRequest["texto_enviar_a_jefe_de_la_dependencia"];
            $datosArray["texto_enviar_a_otra_dependencia"] = $datosRequest["texto_enviar_a_otra_dependencia"];
            $datosArray["texto_regresar_proceso_al_ultimo_usuario"] = $datosRequest["texto_regresar_proceso_al_ultimo_usuario"];
            $datosArray["tipo_actuacion"] = $datosRequest["tipo_actuacion"];
            $datosArray["excluyente"] = $datosRequest["excluyente"];
            $datosArray["cierra_proceso"] = $datosRequest["cierra_proceso"];
            $datosArray["visible"] = $datosRequest["visible"];
            $datosArray["etapa_siguiente"] = $datosRequest["etapa_siguiente"];
            $datosArray["texto_enviar_a_alguien_de_secretaria_comun_dirigido"] = $datosRequest["texto_enviar_a_alguien_de_secretaria_comun_dirigido"];
            $datosArray["texto_enviar_a_alguien_de_secretaria_comun_aleatorio"] = $datosRequest["texto_enviar_a_alguien_de_secretaria_comun_aleatorio"];
            $datosArray["campos"] = isset($datosRequest["campos"]) ? json_encode($datosRequest["campos"]) : [];

            $mas_actuaciones_aux = MasActuacionesModel::where('nombre_actuacion', $datosRequest["nombre_actuacion"])->get();

            if (count($mas_actuaciones_aux) > 0) {
                return [
                    "error" => 'EL NOMBRE "' . $datosRequest["nombre_actuacion"] . '" YA ESTÁ REGISTRADO EN EL SISTEMA.',
                    "mensaje" => 'EL NOMBRE "' . $datosRequest["nombre_actuacion"] . '" YA ESTÁ REGISTRADO EN EL SISTEMA.'
                ];
            }

            // Se inserta el manual
            $respuesta = MasActuacionResource::make($this->repository->create($datosArray));

            $id = $respuesta->id;

            //dd("Error");

            //Actualizar Lista Roles
            RolMasActuacionModel::where('id_mas_actuacion', $id)->delete();
            foreach ($datosRequest['lista_roles'] as $datosRol) {
                $datosRol['created_user'] = auth()->user()->name;
                $datosRol['id_mas_actuacion'] = $id;
                if ($datosRol['estado'] == '1') {
                    RolMasActuacionModel::create($datosRol);
                }
            }

            //Actualizar Lista Etapa
            MasEtapaMasActuacionModel::where('id_mas_actuacion', $id)->delete();
            foreach ($datosRequest['lista_etapa'] as $datosEtapa) {
                $datosEtapa['created_user'] = auth()->user()->name;
                $datosEtapa['id_mas_actuacion'] = $id;
                if ($datosEtapa['estado'] == '1') {
                    //dd("Entro", $datosEtapa);
                    MasEtapaMasActuacionModel::create($datosEtapa);
                }
            }

            // Se decodifica la respuesta
            $array = json_decode(json_encode($respuesta));

            //dd("Error");

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
        return MasActuacionResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MasActuacionesUpdateFormRequest $request, $id)
    {
        try {
            // Se inicializa la conexion
            DB::connection()->beginTransaction();

            // Se capturan los datos
            $datosRequest = $request->validated()["data"]["attributes"];
            $datosRequest["campos"] = isset($datosRequest["campos"]) ? json_encode($datosRequest["campos"]) : [];
            $prev_mas_actuacion = $this->repository->find($id);
            $baseFolderPath = storage_path() . '/files/templates/actuaciones/';

            // Validar el array de las etapas
            $arrayEtapas = [];
            error_log("HOLA");
            error_log(json_encode($datosRequest["id_etapa"]));
            // Se validan que existan etapas
            if (isset($datosRequest["id_etapa"])) {

                $etapas = $datosRequest["id_etapa"];

                if (is_array($etapas)) {

                    // Se recorren las etapas seleccionadas
                    foreach ($etapas as $key => $value) {
                        // Se captura el id de la etapa
                        $id_etapa = $value["value"];

                        // Se añaden las etapas
                        array_push($arrayEtapas, $id_etapa);
                    }
                } else {
                    $id_etapa = $etapas->value;
                    array_push($arrayEtapas, $id_etapa);
                }



                // Se separa el array por comas
                $datosRequest["id_etapa"] = implode(",", $arrayEtapas);
            }

            if (strlen($datosRequest['fileBase64']) > 0) {

                //elimina archivo anterior
                $prev_path = $baseFolderPath . $prev_mas_actuacion->nombre_plantilla;
                if (file_exists($prev_path)) {
                    unlink($prev_path);
                }

                $path = $baseFolderPath . $datosRequest['nombre_plantilla'];

                // Si el archivo existe se cambia nombre de archivo
                if (file_exists($path)) {
                    $datosRequest['nombre_plantilla'] = str_replace(".docx", "_" . substr($this->GUID(), 0, 6) . ".docx", $datosRequest['nombre_plantilla']);
                    $path = $baseFolderPath . $datosRequest['nombre_plantilla'];
                }

                $b64 = $datosRequest['fileBase64'];
                $bin = base64_decode($b64, true);
                file_put_contents($path, $bin);
            } else {
                $datosRequest['nombre_plantilla'] = $prev_mas_actuacion->nombre_plantilla;
            }

            if (strlen($datosRequest['fileBase64_manual']) > 0) {

                //elimina archivo anterior
                $prev_path = $baseFolderPath . $prev_mas_actuacion->nombre_plantilla_manual;
                if ($prev_mas_actuacion->nombre_plantilla_manual != null) {
                    if (file_exists($prev_path)) {
                        unlink($prev_path);
                    }
                }

                $path = $baseFolderPath . $datosRequest['nombre_plantilla_manual'];

                // Si el archivo existe se cambia nombre de archivo
                if (file_exists($path)) {
                    $datosRequest['nombre_plantilla_manual'] = str_replace(".docx", "_" . substr($this->GUID(), 0, 6) . ".docx", $datosRequest['nombre_plantilla_manual']);
                    $path = $baseFolderPath . $datosRequest['nombre_plantilla_manual'];
                }

                $b64 = $datosRequest['fileBase64_manual'];
                $bin = base64_decode($b64, true);
                file_put_contents($path, $bin);
            } else {
                $datosRequest['nombre_plantilla_manual'] = $prev_mas_actuacion->nombre_plantilla_manual;
            }

            if ($prev_mas_actuacion->nombre_actuacion != $datosRequest["nombre_actuacion"]) {
                $mas_actuaciones_aux = MasActuacionesModel::where('nombre_actuacion', $datosRequest["nombre_actuacion"])->get();
                if (count($mas_actuaciones_aux) > 0) {
                    return [
                        "error" => 'EL NOMBRE "' . $datosRequest["nombre_actuacion"] . '" YA ESTÁ REGISTRADO EN EL SISTEMA.',
                        "mensaje" => 'EL NOMBRE "' . $datosRequest["nombre_actuacion"] . '" YA ESTÁ REGISTRADO EN EL SISTEMA.'
                    ];
                }
            }

            $respuesta = MasActuacionResource::make($this->repository->update($datosRequest, $id));
            $array = json_decode(json_encode($respuesta));

            //Actualizar Lista Roles
            RolMasActuacionModel::where('id_mas_actuacion', $id)->delete();
            foreach ($datosRequest['lista_roles'] as $datosRol) {
                $datosRol['created_user'] = auth()->user()->name;
                $datosRol['id_mas_actuacion'] = $id;
                if ($datosRol['estado'] == '1') {
                    RolMasActuacionModel::create($datosRol);
                }
            }

            //Actualizar Lista Etapa
            MasEtapaMasActuacionModel::where('id_mas_actuacion', $id)->delete();
            foreach ($datosRequest['lista_etapa'] as $datosEtapa) {
                $datosEtapa['created_user'] = auth()->user()->name;
                $datosEtapa['id_mas_actuacion'] = $id;
                if ($datosEtapa['estado'] == '1') {
                    MasEtapaMasActuacionModel::create($datosEtapa);
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
     * Obtiene archivo en base64
     *
     */
    public function getArchivoActuacion($id)
    {
        $actuacion = $this->repository->find($id);

        $baseFolderPath = storage_path() . '/files/templates/actuaciones/';
        $path = $baseFolderPath . $actuacion->nombre_plantilla;

        $datos['file_name'] = $actuacion->nombre_plantilla;
        $datos['content_type'] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        $datos["base_64"] = base64_encode(file_get_contents($path));

        return response()->json($datos);
        //return response()->download($path, $actuacion->nombre_plantilla);
    }

    /**
     * Obtiene archivo en base64
     *
     */
    public function getArchivoActuacionManual($id)
    {
        $actuacion = $this->repository->find($id);

        $baseFolderPath = storage_path() . '/files/templates/actuaciones/';
        $path = $baseFolderPath . $actuacion->nombre_plantilla_manual;

        $datos['file_name'] = $actuacion->nombre_plantilla_manual;
        $datos['content_type'] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        $datos["base_64"] = base64_encode(file_get_contents($path));

        return response()->json($datos);
        //return response()->download($path, $actuacion->nombre_plantilla);
    }


    public function getParametrosPlantilla($idActuacion, $id_proceso_disciplinario)
    {
        $actuacion = $this->repository->find($idActuacion);
        $baseFolderPath = storage_path() . '/files/templates/actuaciones/';
        $path = $baseFolderPath . $actuacion->nombre_plantilla;
        $result = $this->wordService->get_document_params($path, $idActuacion);

        $resultados = $this->obtenerConsultasParametros($result['params'], $id_proceso_disciplinario);
        $result['resultados'] = $resultados;

        return response()->json($result);
    }

    /**
     * Retorna plantilla diligenciada con valores de parametros enviados en request
     */
    public function getPlantillaDiligenciada($idActuacion, Request $request)
    {
        $actuacion = $this->repository->find($idActuacion);
        $baseFolderPath = storage_path() . '/files/templates/actuaciones/';
        $path = $baseFolderPath . $actuacion->nombre_plantilla;
        $params = $request->input('data.attributes.params');
        $valor = null;
        $orden = null;
        /*foreach ($params as &$parametro) {
            $valor = explode(",", $parametro['value']);
            $orden = explode(",", $parametro['orden']);
            array_multisort($orden, $valor);
            $parametro['value'] = implode(', ', $valor);
            $parametro['orden'] = implode(', ', $orden);
        }

        unset($parametro);*/
        $result = $this->wordService->replace_document_params($path, $params);

        $datos['file_name'] = $actuacion->nombre_plantilla;
        $datos['content_type'] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        $datos["params"] = $params;
        $datos["base_64"] = base64_encode(file_get_contents($result));
        return response()->json($datos);
    }

    function compararPorOrden($a, $b)
    {
        return $a->orden - $b->orden;
    }

    /**
     * Metodo encargado de buscar las actuaciones por etapa
     */
    public function getActuacionesPorEtapa($idEtapa)
    {
        // Se consultan las actuaciones por etapa
        $mas_actuaciones = MasActuacionesModel::where("estado", "1")
            ->leftjoin('tbint_mas_etapas_mas_actuaciones', 'tbint_mas_etapas_mas_actuaciones.id_mas_actuacion', '=', 'mas_actuaciones.id')
            ->leftjoin('tbint_roles_mas_actuaciones', 'tbint_roles_mas_actuaciones.id_mas_actuacion', '=', 'mas_actuaciones.id')
            ->where("id_mas_etapa", $idEtapa)
            ->where("tbint_roles_mas_actuaciones.id_rol", function ($query) {
                $query->select('role_id')
                    ->distinct()
                    ->from('users_roles')
                    ->whereRaw('users_roles.user_id = ' . auth()->user()->id)
                    ->whereRaw('tbint_roles_mas_actuaciones.id_rol = users_roles.role_id');
            })
            ->orderBy('nombre_actuacion', 'asc')
            ->get();

        //dd($mas_actuaciones);

        $mas_actuaciones = $mas_actuaciones->unique('nombre_actuacion');

        //dd($mas_actuaciones);

        // Se retorna el array
        return MasActuacionCollection::make($mas_actuaciones);
    }

    // Metodo encargado de buscar la actuacion por el nombre
    public function getActuacionesPorNombre($nombre, $idEtapa)
    {
        // Se ejecuta el query
        $mas_actuaciones = MasActuacionesModel::where("nombre_actuacion", $nombre)
            ->where("estado", "1")
            ->where("id_etapa", $idEtapa)
            ->get();

        // Se retorna la información
        return MasActuacionCollection::make($mas_actuaciones);
    }



    private function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    /**
     * Método que busca la actuacion por el id
     *
     */
    public function getDatosMasActuaciones($id)
    {
        $query = $this->repository->customQuery(function ($model) use ($id) {
            return $model->where('id', $id)
                ->get();
        });
        return MasActuacionCollection::make($query);
    }

    /**
     * Metodo encargado de consultar las firmas de las actuaciones
     */
    public function validarUsuarioPendienteFirma($id_actuacion)
    {
        // Se realiza la consulta de las firmas de la actuacion
        $firmaActuaciones = $this->firmasActuaciones($id_actuacion);

        // Se inicializan la variable en 0
        $pendienteFirma = 0;

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
            if ($estadoFirma == 1) {
                $pendienteFirma = 1;
                break;
            }
        }

        // Se valida que cuando haya un pendiente de firma retorna true
        if ($pendienteFirma == 1) {

            // Se retorna que hay por lo menos un usuario pendiente de firma
            return true;
        }

        // Se retorna false
        return false;
    }

    /**
     * Metodo encargado de consumir el servicio encargado de generar el pdf definitivo
     */
    public function convertToPdf($rutaDocumento, $nombreDocumento)
    {
        // Ruta
        // $ruta = $rutaDocumento == "" ? storage_path() . "/files/actuaciones/20221214021755_Plantilla 002. Auto Indagación Previa.docx" : $rutaDocumento;
        $ruta = $rutaDocumento;

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
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
            ->send(
                'POST',
                'https://10.5.5.18:10445/Convert',
                ['body' => json_encode($var)]
            )->json();

        // Se inicializa en false
        $resultado = [];

        // Se captura la fecha
        $año = date("Y");
        $mes = date("m");
        $dia = date("d");
        $hor = date("h");
        $min = date("i");
        $sec = date("s");

        $nombreDocumento = basename($nombreDocumento, ".docx");
        // $nombreDocumento = basename("20221214021755_Plantilla 002. Auto Indagación Previa.docx", ".docx");

        // Se valida cuando el resultado es correcto
        if (isset($response["result"]) && $response["result"] == "Ok") {

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
                    $resultado,
                    [
                        "estado" => true,
                        "rutaPdf" => $rutaRelativaArchivo
                    ]
                );
            }
        } else {

            // Se redeclara el array en estado ok
            array_push(
                $resultado,
                [
                    "estado" => false,
                    "rutaPdf" => $response["message"]
                ]
            );
        }

        // Se retorna el valor
        return $resultado;
    }

    /**
     * Metodo encargado de obtener los roles
     */
    public function obtenerRoles($id)
    {
        $roles = DB::select(
            "
                SELECT
                    r.id AS id_rol,
                    r.name AS nombre_rol,
                    COALESCE((SELECT 1 FROM tbint_roles_mas_actuaciones trma WHERE trma.id_rol = r.id AND trma.id_mas_actuacion = $id), 0) AS estado
                FROM
                    roles r
            "
        );

        $datos['data'] = $roles;

        return json_encode($datos);
    }


    /*
    Esta funcion genera las etapas para los diferentes actuaciones registradas
    */

    public function obtenerEtapas($id)
    {
        $etapas = DB::select(
            "
                SELECT
                    me.id AS id_mas_etapa,
                    me.nombre AS nombre,
                    COALESCE((SELECT 1 FROM tbint_mas_etapas_mas_actuaciones tmema WHERE tmema.id_mas_etapa = me.id AND tmema.id_mas_actuacion = $id), 0) AS estado
                FROM
                    mas_etapa me
                WHERE me.id NOT IN (0, 1, 2, 9)
                AND me.estado = 1
                ORDER BY me.nombre ASC
            "
        );

        $datos['data'] = $etapas;

        return json_encode($datos);
    }
}
