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
use App\Http\Resources\ProcesoDiciplinario\ProcesoDiciplinarioCollection;
use App\Http\Resources\Buscador\BuscadorResource;
use App\Http\Resources\LogConsultas\LogConsultasResource;
use App\Http\Resources\ProcesoDiciplinario\ProcesoDiciplinarioResource;
use App\Repositories\RepositoryGeneric;
use App\Http\Requests\BuscadorFormRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Traits\UtilidadesTrait;
use App\Models\ActuacionesModel;
use App\Models\DatosInteresadoModel;
use App\Models\EvaluacionModel;
use App\Models\InteresadoEntidadPermitidaModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\TempInteresadosModel;
use App\Models\TipoInteresadoModel;
use Error;

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

    public function buscadorGeneral(BuscadorFormRequest $request)
    {

        // Se capturan los datos
        $datosRequest = $request->validated()["data"]["attributes"];
        $sql = "
        SELECT
            pd.uuid as idProcesoDisciplinario,
            mdo.nombre as dependencia,
            pd.radicado,
            mepd.nombre as estado ,
            me.nombre as etapa,
            i.primer_nombre ,
            i.segundo_nombre ,
            i.primer_apellido ,
            i.segundo_apellido ,
            a.descripcion ,
            ac.auto
        FROM
            proceso_disciplinario pd
        INNER JOIN
            mas_estado_proceso_disciplinario mepd ON pd.estado = mepd.id
        OUTER  APPLY
            ( SELECT atc.uuid, atc.descripcion, atc.fecha_registro, atc.id_dependencia,
                   atc.estado, atc.id_proceso_disciplinario, atc.id_etapa, atc.created_user,
                   atc.created_at, atc.fecha_auto
                FROM antecedente atc
                WHERE pd.uuid = atc.id_proceso_disciplinario and ROWNUM = 1
                ORDER BY atc.created_at DESC
            ) a
        OUTER  APPLY
            ( SELECT inte.uuid, inte.id_etapa, inte.id_tipo_interesao, inte.id_tipo_sujeto_procesal, inte.id_proceso_disciplinario,
                inte.tipo_documento, inte.numero_documento, inte.primer_nombre, inte.segundo_nombre, inte.primer_apellido, inte.segundo_apellido,
                inte.id_departamento, inte.id_ciudad, inte.direccion, inte.id_localidad, inte.email, inte.telefono_celular, inte.telefono_fijo,
                inte.id_sexo, inte.id_genero, inte.id_orientacion_sexual, inte.entidad, inte.cargo, inte.tarjeta_profesional, inte.id_dependencia,
                inte.id_tipo_entidad, inte.nombre_entidad, inte.id_entidad, inte.id_funcionario, inte.estado, inte.created_at, inte.created_user,
                inte.folio, inte.id_dependencia_entidad
                FROM interesado inte
                WHERE pd.uuid = inte.id_proceso_disciplinario and ROWNUM = 1
                ORDER BY inte.created_user DESC
            ) i
        OUTER  APPLY
            ( SELECT act.uuid, act.id_actuacion, act.usuario_accion, act.id_estado_actuacion, act.documento_ruta, act.id_etapa, act.id_dependencia,
                act.auto, act.campos_finales FROM actuaciones act
                WHERE pd.uuid = act.uuid_proceso_disciplinario and ROWNUM = 1
                ORDER BY act.created_user DESC
            ) ac
        LEFT JOIN
            mas_tipo_interesado mti ON i.id_tipo_interesao = mti.id
        INNER JOIN
            mas_etapa me ON pd.id_etapa = me.id
        INNER JOIN
            mas_dependencia_origen mdo ON pd.id_dependencia = mdo.id
        WHERE
        ";

        $rta = "";
        $contadorDeRegistros = 0;
        $where = "";

        // contamos cuantos filtros se enviaron
        foreach ($datosRequest as $key => $value) {

            if ($value != null) {

                $where = $key;
                $contadorDeRegistros++;
            }
        }
        $filtro = "";
        $buscarPor = "";

        // determinamos cual es el primer filtro
        if ($contadorDeRegistros > 1) {
            if ($datosRequest["n_expediente"] == $datosRequest[$where]) {
                $filtro = "Translate(upper(pd.radicado),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
                $buscarPor = "'%" . $datosRequest["n_expediente"] . "%'";
                $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
            } else if ($datosRequest["Vigencia"] == $datosRequest[$where]) {
                $filtro = "Translate(upper(pd.vigencia),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
                $buscarPor = "'%" . $datosRequest["Vigencia"] . "%'";
                $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
            } else if ($datosRequest["delegada"] == $datosRequest[$where]) {
                $filtro = "Translate(upper(mdo.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
                $buscarPor = "'%" . $datosRequest["delegada"] . "%'";
                $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
            } else if ($datosRequest["estado_del_expediente"] == $datosRequest[$where]) {
                /*$filtro = "Translate(upper(mepd.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
                $buscarPor = "'%" . $datosRequest["estado_del_expediente"] . "%'";
                $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";*/
                //$filtro = "Translate(upper(mepd.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
                $buscarPor = $datosRequest["estado_del_expediente"];
                $sql = $sql . "pd.estado = " . $buscarPor;
            } else if ($datosRequest["etapa_del_expediente"] == $datosRequest[$where]) {
                $filtro = "Translate(upper(me.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
                $buscarPor = "'%" . $datosRequest["etapa_del_expediente"] . "%'";
                $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
            } else if ($datosRequest["asunto_del_expediente"] == $datosRequest[$where]) {
                $filtro = "Translate(upper(a.descripcion),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
                $buscarPor = "'%" . $datosRequest["asunto_del_expediente"] . "%'";
                $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
            } else if ($datosRequest["nombre_quejoso"] == $datosRequest[$where]) {
                $filtro = "Translate(upper(i.primer_nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
                $buscarPor = "'%" . $datosRequest["nombre_quejoso"] . "%'";
                $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
            } else if ($datosRequest["identificacion_quejoso"] == $datosRequest[$where]) {
                $filtro = "Translate(upper(i.numero_documento),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
                $buscarPor = "'%" . $datosRequest["identificacion_quejoso"] . "%'";
                $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
            } else if ($datosRequest["tipo_quejoso"] == $datosRequest[$where]) {
                $filtro = "Translate(upper(mti.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
                $buscarPor = "'%" . $datosRequest["tipo_quejoso"] . "%'";
                $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
            }
        }


        if ($datosRequest["n_expediente"] != null && $contadorDeRegistros == 1) {
            $filtro = "Translate(upper(pd.radicado),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["n_expediente"] . "%'";
            $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        } else if ($datosRequest["n_expediente"] != null && $contadorDeRegistros > 1 && $datosRequest["n_expediente"] != $datosRequest[$where]) {
            $filtro = "Translate(upper(pd.radicado),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["n_expediente"] . "%'";
            $sql = $sql . " and " . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        }

        if ($datosRequest["Vigencia"] != null && $contadorDeRegistros == 1) {
            $filtro = "Translate(upper(pd.vigencia),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["Vigencia"] . "%'";
            $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        } else if ($datosRequest["Vigencia"] != null && $contadorDeRegistros > 1 && $datosRequest["Vigencia"] != $datosRequest[$where]) {
            $filtro = "Translate(upper(pd.vigencia),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["Vigencia"] . "%'";
            $sql = $sql . " and " . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        }

        if ($datosRequest["delegada"] != null && $contadorDeRegistros == 1) {
            $filtro = "Translate(upper(mdo.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["delegada"] . "%'";
            $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        } else if ($datosRequest["delegada"] != null && $contadorDeRegistros > 1 && $datosRequest["delegada"] != $datosRequest[$where]) {
            $filtro = "Translate(upper(mdo.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["delegada"] . "%'";
            $sql = $sql . " and " . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        }

        if ($datosRequest["estado_del_expediente"] != null && $contadorDeRegistros == 1) {
            //$filtro = "Translate(upper(mepd.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = $datosRequest["estado_del_expediente"];
            $sql = $sql . $filtro . " pd.estado = " . $datosRequest["estado_del_expediente"];
        } else if ($datosRequest["estado_del_expediente"] != null && $contadorDeRegistros > 1 && $datosRequest["estado_del_expediente"] != $datosRequest[$where]) {
            /* $filtro = "Translate(upper(mepd.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["estado_del_expediente"] . "%'";
            $sql = $sql . " and " . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";*/
            $buscarPor = $datosRequest["estado_del_expediente"];
            $sql = $sql . $filtro . " pd.estado = " . $datosRequest["estado_del_expediente"];
        }

        if ($datosRequest["etapa_del_expediente"] != null && $contadorDeRegistros == 1) {
            $filtro = "Translate(upper(me.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["etapa_del_expediente"] . "%'";
            $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        } else if ($datosRequest["etapa_del_expediente"] != null && $contadorDeRegistros > 1 && $datosRequest["etapa_del_expediente"] != $datosRequest[$where]) {
            $filtro = "Translate(upper(me.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["etapa_del_expediente"] . "%'";
            $sql = $sql . " and " . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        }

        if ($datosRequest["asunto_del_expediente"] != null && $contadorDeRegistros == 1) {
            $filtro = "Translate(upper(a.descripcion),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["asunto_del_expediente"] . "%'";
            $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        } else if ($datosRequest["asunto_del_expediente"] != null && $contadorDeRegistros > 1 && $datosRequest["asunto_del_expediente"] != $datosRequest[$where]) {
            $filtro = "Translate(upper(a.descripcion),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["asunto_del_expediente"] . "%'";
            $sql = $sql . " and " . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        }

        if ($datosRequest["nombre_quejoso"] != null && $contadorDeRegistros == 1) {
            $filtro = "Translate(upper(i.primer_nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["nombre_quejoso"] . "%'";
            $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        } else if ($datosRequest["nombre_quejoso"] != null && $contadorDeRegistros > 1 && $datosRequest["nombre_quejoso"] != $datosRequest[$where]) {
            $filtro = "Translate(upper(i.primer_nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["nombre_quejoso"] . "%'";
            $sql = $sql . " and " . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        }

        if ($datosRequest["identificacion_quejoso"] != null && $contadorDeRegistros == 1) {
            $filtro = "Translate(upper(i.numero_documento),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["identificacion_quejoso"] . "%'";
            $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        } else if ($datosRequest["identificacion_quejoso"] != null && $contadorDeRegistros > 1 && $datosRequest["identificacion_quejoso"] != $datosRequest[$where]) {
            $filtro = "Translate(upper(i.numero_documento),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["identificacion_quejoso"] . "%'";
            $sql = $sql . " and " . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        }

        if ($datosRequest["tipo_quejoso"] != null && $contadorDeRegistros == 1) {
            $filtro = "Translate(upper(mti.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["tipo_quejoso"] . "%'";
            $sql = $sql . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        } else if ($datosRequest["tipo_quejoso"] != null && $contadorDeRegistros > 1 && $datosRequest["tipo_quejoso"] != $datosRequest[$where]) {
            $filtro = "Translate(upper(mti.nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')";
            $buscarPor = "'%" . $datosRequest["tipo_quejoso"] . "%'";
            $sql = $sql . " and " . $filtro . " like UPPER(" . BuscadorController::eliminar_tildes($buscarPor) . ")";
        }

        error_log(json_encode($sql));

        $rta = DB::select($sql);

        if ($rta != "") {

            $arr = array();

            $this->repository->setModel(new ProcesoDiciplinarioModel());
            /*foreach ($rta as $key => $value) {

                $query = $this->repository->customQuery(function ($model) use ($rta, $key) {
                    return $model
                        ->where('log_proceso_disciplinario.id_proceso_disciplinario', $rta[$key]->idprocesodisciplinario)
                        ->where('log_proceso_disciplinario.id_funcionario_actual', "!=", null)
                        ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                        ->select(
                            'proceso_disciplinario.uuid',
                            'proceso_disciplinario.radicado',
                            'proceso_disciplinario.vigencia',
                            'proceso_disciplinario.estado',
                            'proceso_disciplinario.id_tipo_proceso',
                        )->orderBy('log_proceso_disciplinario.CREATED_AT', 'desc')->get();
                });

                // Arreglando que mustre los interesados
                $interesado = new RepositoryGeneric();
                $interesado->setModel(new DatosInteresadoModel());
                $queryInteresado = $interesado->customQuery(function ($model) use ($rta, $key) {
                    return
                        $model
                        ->join('mas_tipo_interesado', 'interesado.id_tipo_interesao', '=', 'mas_tipo_interesado.id')
                        ->where('mas_tipo_interesado.estado', true)
                        ->where('interesado.id_proceso_disciplinario', $rta[$key]->idprocesodisciplinario)
                        ->orderByDesc('interesado.created_at')
                        ->get();
                })->first();
                $rta[$key]->primer_nombre = $queryInteresado->primer_nombre;
                $rta[$key]->segundo_nombre = $queryInteresado->segundo_nombre;
                $rta[$key]->primer_apellido  = $queryInteresado->primer_apellido;
                $rta[$key]->segundo_apellido = $queryInteresado->segundo_apellido;
                $identificacion_interesado = $queryInteresado->numero_documento;
                $tipo_interesado = $queryInteresado->nombre;
                $cargo_interesado = $queryInteresado->cargo;


                // Agregando que muestre el tipo de conducta
                $evaluacion = new RepositoryGeneric();
                $evaluacion->setModel(new EvaluacionModel());
                $queryEvaluacion = $evaluacion->customQuery(function ($model) use ($rta, $key) {
                    return
                        $model
                        ->join('mas_tipo_conducta', 'evaluacion.tipo_conducta', '=', 'mas_tipo_conducta.id')
                        ->where('mas_tipo_conducta.estado', true)
                        ->where('evaluacion.id_proceso_disciplinario', $rta[$key]->idprocesodisciplinario)
                        ->orderByDesc('evaluacion.created_at')
                        ->get();
                })->first();
                $tipo_conducta = $queryEvaluacion ? $queryEvaluacion->nombre : null;


                // Agregando que muestre el numero de auto y tipo de auto
                $actuacion = new RepositoryGeneric();
                $actuacion->setModel(new ActuacionesModel());
                $queryActuacion = $actuacion->customQuery(function ($model) use ($rta, $key) {
                    return
                        $model
                        ->join('mas_actuaciones', 'actuaciones.id_actuacion', '=', 'mas_actuaciones.id')
                        ->where('mas_actuaciones.estado', true)
                        ->where('actuaciones.uuid_proceso_disciplinario', $rta[$key]->idprocesodisciplinario)
                        ->orderByDesc('mas_actuaciones.created_at')
                        ->get();
                })->first();
                $tipo_auto = $queryActuacion ? $queryActuacion->nombre_actuacion : null;
                $auto = $queryActuacion ? $queryActuacion->auto : null;
                $fecha_auto = $queryActuacion ? date("d/m/Y h:i:s A", strtotime($queryActuacion->created_at)) : null;


                // Conocer en donde esta el proceso
                $logProceso = new RepositoryGeneric();
                $logProceso->setModel(new LogProcesoDisciplinarioModel());
                $queryLogProceso = $logProceso->customQuery(function ($model) use ($rta, $key) {
                    return
                        $model
                        ->leftJoin('users', 'log_proceso_disciplinario.id_funcionario_actual', '=', 'users.name')
                        ->join('mas_dependencia_origen', 'users.id_dependencia', '=', 'mas_dependencia_origen.id')
                        ->where('mas_dependencia_origen.estado', true)
                        ->where('log_proceso_disciplinario.id_proceso_disciplinario', $rta[$key]->idprocesodisciplinario)
                        ->whereNotNull('log_proceso_disciplinario.id_funcionario_actual')
                        ->get();
                })->first();
                $ubicacion_actual = $queryLogProceso ? $queryLogProceso->nombre : null;

                array_push(
                    $arr,
                    array(
                        "type" => "buscador",
                        "attributes" => array(
                            "proceso_disciplinario" => MisPendientesCollection::make($query)->first(),
                            "Id" => $rta[$key]->radicado,
                            "Dependencia" => $rta[$key]->dependencia,
                            "Numero_expediente" => $rta[$key]->radicado,
                            "Estado_expediente" => $rta[$key]->estado,
                            "Nombre_interesado" => $rta[$key]->primer_nombre == "ANÓNIMO(A)" ? $rta[$key]->primer_nombre :
                                $rta[$key]->primer_nombre . " " . $rta[$key]->segundo_nombre . " " . $rta[$key]->primer_apellido . " " . $rta[$key]->segundo_apellido,
                            "Identificacion_interesado" => $identificacion_interesado,
                            "Tipo_interesado" => $tipo_interesado,
                            "Cargo_interesado" => $cargo_interesado,
                            "Etapa_expediente" => $rta[$key]->etapa,
                            "Asunto_del_expediente" => $rta[$key]->descripcion,
                            "Tipo_conducta" => $tipo_conducta,
                            "Tipo_auto" => $tipo_auto,
                            "Fecha_auto" => $fecha_auto,
                            "Ubicacion_actual" => $ubicacion_actual,
                            "Auto" => $auto,
                            "Version" => "Disciplinarios",
                            "migracion" => 0,
                        )
                    )
                );
            */}

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
                            "attributes" => array(
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
                            )
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
        } else {
            return json_encode("Error");
        }
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
