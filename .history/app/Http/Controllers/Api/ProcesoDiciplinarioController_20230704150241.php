<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AntecedenteTrait;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Controllers\Traits\NumeroCasosTrait;
use App\Http\Controllers\Traits\SiriusTrait;
use App\Http\Controllers\Traits\UtilidadesTrait;
use App\Http\Requests\ProcesoDesgloseFormRequest;
use App\Http\Requests\ProcesoDiciplinarioFormRequest;
use App\Http\Requests\ProcesoSinprocFormRequest;
use App\Http\Requests\ProcesoDiciplinarioRemisionFormRequest;
use App\Http\Requests\ProcesoPoderPreferenteFormRequest;
use App\Http\Requests\SearchRadicadoFormRequest;
use App\Http\Requests\TrasladoMasivoFormRequest;
use App\Http\Resources\ClasificacionRadicado\ClasificacionRadicadoResource;
use App\Http\Resources\DependenciaOrigen\DependenciaOrigenResource;
use App\Http\Resources\EntidadInvestigado\EntidadInvestigadoResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Resources\ProcesoDesglose\ProcesoDesgloseResource;
use App\Http\Resources\ProcesoDiciplinario\ProcesoDiciplinarioCollection;
use App\Http\Resources\ProcesoDiciplinario\ProcesoDiciplinarioResource;
use App\Http\Resources\ProcesoPoderPreferente\ProcesoPoderPreferenteResource;
use App\Http\Resources\ProcesoSinproc\ProcesoSinprocResource;
use App\Http\Resources\ProcesoSirius\ProcesoSiriusResource;
use App\Http\Resources\RemisionQueja\RemisionQuejaResource;
use App\Http\Resources\ValidarClasificacion\ValidarClasificacionResource;
use App\Http\Utilidades\Constants;
use App\Models\ActuacionesModel;
use App\Models\AntecedenteModel;
use App\Models\ClasificacionRadicadoModel;
use App\Models\ConsecutivoDesgloseModel;
use App\Models\DependenciaOrigenModel;
use App\Models\EntidadInvestigadoModel;
use App\Models\EntidadModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ProcesoDesgloseModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\ProcesoPoderPreferenteModel;
use App\Models\ProcesoSinprocModel;
use App\Models\ProcesoSiriusModel;
use App\Models\RemisionQuejaModel;
use App\Models\SecuenciaModel;
use App\Models\TramiteUsuarioModel;
use App\Models\User;
use App\Models\UsuarioRolModel;
use App\Models\ValidarClasificacionModel;
use App\Models\VigenciaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcesoDiciplinarioController extends Controller
{
    use SiriusTrait;
    use AntecedenteTrait;
    use LogTrait;
    use UtilidadesTrait;
    use NumeroCasosTrait;

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ProcesoDiciplinarioModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ProcesoDiciplinarioCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    //

    //public function validarProcesoSirius(ProcesoSiriusFormRequest $request)
    public function validarProcesoSirius(SearchRadicadoFormRequest $request)
    {
        //INICIO PROCESO DE VALIDACION
        //1. Validación por SIRIUS
        $datosRequest = $request->validated()["data"]["attributes"];
        return $this->validarProcesoSiriusLocal($datosRequest);
    }

    public function validarProcesoSiriusLocal(array $datosRequest)
    {
        //INICIO PROCESO DE VALIDACION
        //1. Validación por SIRIUS

        $respuestaSirius = $this->buscarRadicado2($datosRequest['radicado']);

        //2. Validación en la BD
        $obj_sirius['radicado'] = substr($datosRequest['radicado'], 8, 7);

        if ($obj_sirius['radicado'][0] == 0) {
            $obj_sirius['radicado'] = substr($obj_sirius['radicado'], 1);
        }

        $obj_sirius['tipo_radicacion'] = substr($datosRequest['radicado'], 5, 2);
        $obj_sirius['vigencia_origen'] = substr($datosRequest['radicado'], 0, 4);
        $obj_sirius['vigencia'] = substr($datosRequest['radicado'], 0, 4);

        $queryRadicado = $this->repository->customQuery(function ($model) use ($obj_sirius) {
            return $model->where('radicado', $obj_sirius['radicado'])
                ->where('tipo_radicacion', $obj_sirius['tipo_radicacion'])
                ->where('vigencia_origen', $obj_sirius['vigencia_origen'])
                ->get();
        });

        if (count($queryRadicado) > 0) {
            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'El radicado ya se encuentra en uso';
            return json_encode($error);
        }


        //Obtener fecha
        if (json_encode($respuestaSirius['correspondencia']) != 'null') {
            $fechaRadicado = json_encode($respuestaSirius['correspondencia']['fecRadicado'], JSON_UNESCAPED_UNICODE);
            $fechaRadicado = substr($fechaRadicado, 1);
            $fechaRadicado = substr($fechaRadicado, 0, -1);
            $datosFecha = explode("T", $fechaRadicado);
            $fecRad = $datosFecha[0];
            $horaRad = $datosFecha[1];
            $datosHora = explode(".", $horaRad);
            $horaRad = $datosHora[0];

            $tipoRadicacion = substr($datosRequest['radicado'], 5, 2);

            //Obtener el tipo de usuario
            if ($respuestaSirius == 'ER') {
                $tipoPersona = json_encode($respuestaSirius['agenteList'][0]['personDTO']['personTypeCode']);   //: TP-PERA  //TP-PERPN   //TP-PERPJ
                $tipoPersona = substr($tipoPersona, 1);
                $tipoPersona = substr($tipoPersona, 0, -1);

                if ($tipoPersona == 'TP-PERA') {
                    $nomRemitente = 'Registro Anonimo';
                    $tdocRemitente = 'Sin tipo de documento';
                    $idenRemitente = 'Sin número de identificación';
                } elseif ($tipoPersona == 'TP-PERPN') {
                    $tdocRemitente = json_encode($respuestaSirius['agenteList'][0]['personDTO']['idDocTypeCode'], JSON_UNESCAPED_UNICODE);
                    $tdocRemitente = substr($tdocRemitente, 1);
                    $tdocRemitente = substr($tdocRemitente, 0, -1);
                    $tdocRemitente = $this->textoTipoDocumento($tdocRemitente);

                    $idenRemitente = json_encode($respuestaSirius['agenteList'][0]['personDTO']['idNumber'], JSON_UNESCAPED_UNICODE);
                    $idenRemitente = substr($idenRemitente, 1);
                    $idenRemitente = substr($idenRemitente, 0, -1);

                    $nomRemitente = json_encode($respuestaSirius['agenteList'][0]['personDTO']['name'], JSON_UNESCAPED_UNICODE);
                    $nomRemitente = substr($nomRemitente, 1);
                    $nomRemitente = substr($nomRemitente, 0, -1);
                } elseif ($tipoPersona == 'TP-PERPJ') {
                    $tdocRemitente = json_encode($respuestaSirius['agenteList'][0]['personDTO']['idDocTypeCode'], JSON_UNESCAPED_UNICODE);
                    $tdocRemitente = substr($tdocRemitente, 1);
                    $tdocRemitente = substr($tdocRemitente, 0, -1);
                    $tdocRemitente = $this->textoTipoDocumento($tdocRemitente);

                    $idenRemitente = json_encode($respuestaSirius['agenteList'][0]['personDTO']['nit'], JSON_UNESCAPED_UNICODE);
                    $idenRemitente = substr($idenRemitente, 1);
                    $idenRemitente = substr($idenRemitente, 0, -1);

                    $nomRemitente = json_encode($respuestaSirius['agenteList'][0]['personDTO']['businessName'], JSON_UNESCAPED_UNICODE);
                    $nomRemitente = substr($nomRemitente, 1);
                    $nomRemitente = substr($nomRemitente, 0, -1);
                }
            } elseif ($tipoRadicacion == 'EE') {
                $tipoPersona = json_encode($respuestaSirius['agenteList'][1]['personDTO']['personTypeCode']);   //: TP-PERA  //TP-PERPN   //TP-PERPJ
                $tipoPersona = substr($tipoPersona, 1);
                $tipoPersona = substr($tipoPersona, 0, -1);

                if ($tipoPersona == 'TP-PERA') {
                    $nomRemitente = 'Registro Anonimo';
                    $tdocRemitente = 'Sin tipo de documento';
                    $idenRemitente = 'Sin número de identificación';
                } elseif ($tipoPersona == 'TP-PERPN') {
                    $tdocRemitente = json_encode($respuestaSirius['agenteList'][1]['personDTO']['idDocTypeCode'], JSON_UNESCAPED_UNICODE);
                    $tdocRemitente = substr($tdocRemitente, 1);
                    $tdocRemitente = substr($tdocRemitente, 0, -1);
                    $tdocRemitente = $this->textoTipoDocumento($tdocRemitente);

                    $idenRemitente = json_encode($respuestaSirius['agenteList'][1]['personDTO']['idNumber'], JSON_UNESCAPED_UNICODE);
                    $idenRemitente = substr($idenRemitente, 1);
                    $idenRemitente = substr($idenRemitente, 0, -1);

                    $nomRemitente = json_encode($respuestaSirius['agenteList'][1]['personDTO']['name'], JSON_UNESCAPED_UNICODE);
                    $nomRemitente = substr($nomRemitente, 1);
                    $nomRemitente = substr($nomRemitente, 0, -1);
                } elseif ($tipoPersona == 'TP-PERPJ') {
                    $tdocRemitente = json_encode($respuestaSirius['agenteList'][1]['personDTO']['idDocTypeCode'], JSON_UNESCAPED_UNICODE);
                    $tdocRemitente = substr($tdocRemitente, 1);
                    $tdocRemitente = substr($tdocRemitente, 0, -1);
                    $tdocRemitente = $this->textoTipoDocumento($tdocRemitente);

                    $idenRemitente = json_encode($respuestaSirius['agenteList'][1]['personDTO']['nit'], JSON_UNESCAPED_UNICODE);
                    $idenRemitente = substr($idenRemitente, 1);
                    $idenRemitente = substr($idenRemitente, 0, -1);

                    $nomRemitente = json_encode($respuestaSirius['agenteList'][1]['personDTO']['businessName'], JSON_UNESCAPED_UNICODE);
                    $nomRemitente = substr($nomRemitente, 1);
                    $nomRemitente = substr($nomRemitente, 0, -1);
                }
            } elseif ($tipoRadicacion == 'IE') {
                $nomRemitente = json_encode($respuestaSirius['agenteList'][0]['name'], JSON_UNESCAPED_UNICODE);
                $nomRemitente = substr($nomRemitente, 1);
                $nomRemitente = substr($nomRemitente, 0, -1);
                $tdocRemitente = 'Cod. Dependencia';
                $idenRemitente = json_encode($respuestaSirius['agenteList'][0]['idNumber'], JSON_UNESCAPED_UNICODE);
                $idenRemitente = substr($idenRemitente, 1);
                $idenRemitente = substr($idenRemitente, 0, -1);
            }

            //$reciboDatos['antecedente'] = "Fecha Radicación: $fecRad - $horaRad \n";
            $reciboDatos['antecedente'] = $respuestaSirius['correspondencia']['descripcion'] . "\n";
            //$reciboDatos['antecedente'] .= "$nomRemitente ($tdocRemitente: $idenRemitente)";
            $reciboDatos['fecha_cordis'] = $fecRad . " - " . $horaRad;
            $reciboDatos['id_origen_radicado'] = $datosRequest['id_origen_radicado'];
            $json['data']['attributes'] = $reciboDatos;
            return json_encode($json);
        } else {
            $error['estado'] = false;
            $error['error'] = 'El radicado ' . $datosRequest['radicado'] . ' no existe';
            return json_encode($error);
        }
    }

    function textoTipoDocumento($dato)
    {
        switch ($dato) {
            case 'TP-DOCP':
                $txt = 'Pasaporte';
                break;
            case 'TP-DOCT':
                $txt = 'Tarjeta de identidad';
                break;
            case 'TP-DOCR':
                $txt = 'Registro civil';
                break;
            case 'TP-DOCCE':
                $txt = 'Cedula de extranjería';
                break;
            case 'TP-DOCCC':
                $txt = 'Cedula de ciudadanía';
                break;
            case 'TP-DOCN':
                $txt = 'Número de Identificación Tributario';
                break;
            default:
                $txt = 'Indentificación Sin Definir';
                break;
        }
        unset($dato);
        return ($txt);
    }

    /**
     *
     */
    public function validarProcesoDesglose(ProcesoDesgloseFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        //INICIO PROCESO DE VALIDACION
        //1. Validación por SINPROC
        return $this->validarProcesoDesgloseLocal($datosRequest);
    }

    /**
     *
     */
    public function validarProcesoDesgloseLocal(array $datosRequest)
    {
        $reciboDatos['antecedente'] = "";

        //INICIO PROCESO DE VALIDACION EN NUESTRO SISTEMA
        $queryRadicado = $this->repository->customQuery(function ($model) use ($datosRequest) {
            return $model->where('radicado', $datosRequest['radicado'])
                ->where('vigencia', $datosRequest['vigencia'])
                ->get();
        })->first();

        // Si existe el registro en nuestro sistema
        if (!empty($queryRadicado)) {

            // Consulto el registro de antecendentes del proceso
            $this->repository->setModel(new AntecedenteModel());
            $querySinproc = $this->repository->customQuery(function ($model) use ($queryRadicado) {
                return
                    $model->where('id_proceso_disciplinario', $queryRadicado->uuid)
                    ->where('estado', 1)
                    ->get();
            })->first();

            // Consulto el registro de clasificacion de radicado del proceso
            $this->repository->setModel(new ClasificacionRadicadoModel());
            $queryClasificacion = $this->repository->customQuery(function ($model) use ($queryRadicado) {
                return
                    $model->where('id_proceso_disciplinario', $queryRadicado->uuid)
                    ->where('estado', 1)
                    ->get();
            })->first();

            if ($queryClasificacion != null) {
                if ($queryRadicado?->id_etapa >= Constants::ETAPA['evaluacion_pd']) {

                    //Informacion de desglose
                    //$this->repository->setModel(new ActuacionesModel());
                    // $queryActuacion = $this->repository->customQuery(function ($model) use ($queryRadicado) {
                    //     return
                    //         $model->where('uuid_proceso_disciplinario', $queryRadicado->uuid)
                    //         ->where('id_actuacion', 13)
                    //         ->get();
                    // })->first();

                    $queryActuacion = DB::select(
                        "
                            SELECT
                                a.auto,
                                a.id_dependencia,
                                a.id_estado_actuacion,
                                mdo.nombre AS nombre_dependencia,
                                a.created_at
                            FROM
                                actuaciones a
                            INNER JOIN mas_dependencia_origen mdo ON a.id_dependencia = mdo.id
                            WHERE a.uuid_proceso_disciplinario = '" . $queryRadicado->uuid . "'
                            AND a.id_actuacion = 13"
                    );

                    if (!empty($queryActuacion)) {
                        if (empty($queryActuacion[0]->auto)) {
                            $error['estado'] = false;
                            $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' no tiene un auto-desglose asignado.';
                            return json_encode($error);
                        } else {
                            $reciboDatos['desglose']['auto'] = $queryActuacion[0]->auto;
                            $reciboDatos['desglose']['id_dependencia'] = $queryActuacion[0]->id_dependencia;
                            $reciboDatos['desglose']['nombre_dependencia'] = $queryActuacion[0]->nombre_dependencia;
                            $reciboDatos['desglose']['created_at'] = date('Y-m-d', strtotime($queryActuacion[0]->created_at));
                        }
                    } else {
                        $error['estado'] = false;
                        $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' no tiene un auto-desglose asignado.';
                        return json_encode($error);
                    }

                    // Tomo el ultimo antecedente del expediente
                    $reciboDatos['antecedente'] = $querySinproc->descripcion;
                    $reciboDatos['clasificacion']['uuid'] = $queryClasificacion->uuid;
                    $reciboDatos['clasificacion']['id_proceso_disciplinario'] = $queryClasificacion->id_proceso_disciplinario;
                    $reciboDatos['clasificacion']['id_etapa'] = $queryClasificacion->id_etapa;
                    $reciboDatos['clasificacion']['id_tipo_expediente'] = $queryClasificacion->id_tipo_expediente;
                    $reciboDatos['clasificacion']['observaciones'] = $queryClasificacion->observaciones;
                    $reciboDatos['clasificacion']['id_tipo_queja'] = $queryClasificacion->id_tipo_queja;
                    $reciboDatos['clasificacion']['id_termino_respuesta'] = $queryClasificacion->id_termino_respuesta;
                    $reciboDatos['clasificacion']['fecha_termino'] = $queryClasificacion->fecha_termino;
                    $reciboDatos['clasificacion']['hora_termino'] = $queryClasificacion->hora_termino;
                    $reciboDatos['clasificacion']['gestion_juridica'] = $queryClasificacion->gestion_juridica;
                    $reciboDatos['clasificacion']['estado'] = $queryClasificacion->estado;
                    $reciboDatos['clasificacion']['id_estado_reparto'] = $queryClasificacion->id_estado_reparto;
                    $reciboDatos['clasificacion']['oficina_control_interno'] = $queryClasificacion->oficina_control_interno;
                    $reciboDatos['clasificacion']['id_tipo_derecho_peticion'] = $queryClasificacion->id_tipo_derecho_peticion;
                    $reciboDatos['clasificacion']['reclasificacion'] = $queryClasificacion->reclasificacion;
                    $reciboDatos['clasificacion']['id_dependencia'] = $queryClasificacion->id_dependencia;
                    $reciboDatos['clasificacion']['validacion_jefe'] = $queryClasificacion->validacion_jefe;
                    $reciboDatos['dependenciaOrigen'] = $queryRadicado->id_dependencia;
                } else {
                    $error['estado'] = false;
                    "messageDetail";
                    $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' aún se encuentra en una etapa muy temprana';
                    return json_encode($error);
                }
            } else {
                $error['estado'] = false;
                "messageDetail";
                $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' aún no se encuentra clasificado';
                return json_encode($error);
            }


            // si NO existe el registro en nuestro sistema
        } else {

            $migracion = new RepositoryGeneric();
            $migracion_version = new MigracionController($migracion);
            $versionMigracion = $migracion_version->getInfoVersion($datosRequest['radicado'], $datosRequest['vigencia']);

            if ($versionMigracion == "Proceso disciplinario no encontrado en migración") {
                $error['estado'] = false;
                "messageDetail";
                $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' con vigencia ' . $datosRequest['vigencia'] . ' no existe';
                return json_encode($error);
            } else {
                $error['estado'] = false;
                "messageDetail";
                $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' con vigencia ' . $datosRequest['vigencia'] . ' no ha sido migrado.';
                return json_encode($error);
            }
        }




        $json['data']['attributes'] = $reciboDatos;
        return json_encode($json);
    }

    public function validarProcesoSinproc(ProcesoSinprocFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        return $this->validarProcesoSinprocLocal($datosRequest);
    }


    /**
     * Validar los datos de entrada de poder preferente. Son los mismo campos del proceso sinproc.
     */
    public function validarProcesoPoderPreferente(ProcesoPoderPreferenteFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        return $this->validarProcesoPreferente($datosRequest);
    }

    /**
     *
     */
    public function validarProcesoSinprocLocal(array $datosRequest)
    {

        //INICIO PROCESO DE VALIDACION
        //1. Validación por SINPROC
        $repository_tramiteusuario = new RepositoryGeneric();
        $repository_tramiteusuario->setModel(new TramiteusuarioModel());

        //Validación en nuestro sistema
        $queryRadicado = $this->repository->customQuery(function ($model) use ($datosRequest) {
            return $model->where('radicado', $datosRequest['radicado'])
                ->where('vigencia_origen', $datosRequest['vigencia'])
                ->get();
        });

        if (count($queryRadicado) > 0) {
            $error['estado'] = false;
            $error['error'] = 'El SINPROC ' . $datosRequest['radicado'] . ' con vigencia ' . $datosRequest['vigencia'] . ' ya se encuentra registrado. Verifique los datos ingresados e intente nuevamente su registro. Si persiste esta alerta, por favor informe a la Dirección TIC.';
            return json_encode($error);
        }

        //Existe en tramiteusuario
        $querySinproc = $repository_tramiteusuario->customQuery(function ($model) use ($datosRequest) {
            return
                $model->where('num_solicitud', $datosRequest['radicado'])->where('vigencia', $datosRequest['vigencia'])->get();
        });

        if (count($querySinproc) == 0) {
            $error['estado'] = false;
            $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' con vigencia ' . $datosRequest['vigencia'] . ' no se encuentra registrado en la base de datos, verifíquelo y vuelva a intentar su validación.';
            return json_encode($error);
        }

        //Es requerimiento ciudadano
        $querySinproc = $repository_tramiteusuario->customQuery(function ($model) use ($datosRequest) {
            return
                $model->where('num_solicitud', $datosRequest['radicado'])
                ->where('vigencia', $datosRequest['vigencia'])
                ->whereIn('id_tramite', array(240, 56, 200, 290, 206, 207, 208, 126)) //Requerimiento Ciudadano
                ->get();
        });

        if (count($querySinproc) == 0) {
            $error['estado'] = false;
            $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' ingresado no corresponde a un requerimiento ciudadano.';
            return json_encode($error);
        }

        $reciboDatos['antecedente'] = $querySinproc[0]->texto08;
        $json['data']['attributes'] = $reciboDatos;
        return json_encode($json);
    }


    /**
     *
     */
    public function validarProcesoPreferente(array $datosRequest)
    {
        //INICIO PROCESO DE VALIDACION
        //1. Validación por SINPROC
        $repository_tramiteusuario = new RepositoryGeneric();
        $repository_tramiteusuario->setModel(new TramiteusuarioModel());

        //Existe en tramiteusuario
        $querySinproc = $repository_tramiteusuario->customQuery(function ($model) use ($datosRequest) {
            return
                $model->where('num_solicitud', $datosRequest['radicado'])->where('vigencia', $datosRequest['vigencia'])->get();
        });

        if (count($querySinproc) == 0) {
            $error['estado'] = false;
            $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' con vigencia ' . $datosRequest['vigencia'] . ' no se encuentra registrado en la base de datos, verifíquelo y vuelva a intentar su validación.';
            return json_encode($error);
        }

        //Es requerimiento ciudadano
        $querySinproc = $repository_tramiteusuario->customQuery(function ($model) use ($datosRequest) {
            return
                $model->where('num_solicitud', $datosRequest['radicado'])
                ->where('vigencia', $datosRequest['vigencia'])
                ->whereIn('id_tramite', array(302)) //Requerimiento Ciudadano
                ->get();
        });

        if (count($querySinproc) == 0) {
            $error['estado'] = false;
            $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' no corresponde a un código 302.';
            return json_encode($error);
        }

        //Que el tramite este finalizado
        $querySinproc = $repository_tramiteusuario->customQuery(function ($model) use ($datosRequest) {
            return
                $model->where('num_solicitud', $datosRequest['radicado'])
                ->where('vigencia', $datosRequest['vigencia'])
                ->where('estado_tramite', 'Finalizado')
                ->get();
        });

        if (empty($querySinproc[0])) {
            $error['estado'] = false;
            $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' ingresado corresponde a un poder preferente que se encuentra remitido (en proceso de gestión por parte de la entidad.';
            return json_encode($error);
        }

        $querySinproc = $repository_tramiteusuario->customQuery(function ($model) use ($datosRequest) {
            return
                $model->where('num_solicitud', $datosRequest['radicado'])
                ->where('vigencia', $datosRequest['vigencia'])
                ->whereIn('numero03', array(0, 1, 2, 3))
                ->get();
        });


        if (count($querySinproc) > 0) {

            if ($querySinproc[0]->numero03 == 0) {
                $error['estado'] = false;
                $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' está en proceso de evaluación.';
                return json_encode($error);
            } else if ($querySinproc[0]->numero03 == 1) {
                $error['estado'] = false;
                $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' no asumirlo .';
                return json_encode($error);
            } else if ($querySinproc[0]->numero03 == 2) {
                $error['estado'] = false;
                $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' ejercer supervigilacia .';
                return json_encode($error);
            }
        }

        if (count($querySinproc) == 0) {
            $error['estado'] = false;
            $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' no corresponde a un código 302.';
            return json_encode($error);
        }

        //2. Validación en nuestro sistema
        $queryRadicado = $this->repository->customQuery(function ($model) use ($datosRequest) {
            return $model->where('radicado', $datosRequest['radicado'])->get();
        });

        if (count($queryRadicado) > 0) {
            $error['estado'] = false;
            $error['error'] = 'El SINPROC ' . $datosRequest['radicado'] . ' con vigencia ' . $datosRequest['vigencia'] . ' ya se encuentra registrado. Verifique los datos ingresados e intente nuevamente su registro. Si persiste esta alerta, por favor informe a la Dirección TIC.';
            return json_encode($error);
        }

        //3. VALIDAR EL NOMBRE DE LA DEPENDENCIA CARGO EN LA COLUMNA CODIGO_HOMOLOGADO
        $repository_dependencia = new RepositoryGeneric();
        $repository_dependencia->setModel(new DependenciaOrigenModel());
        $dependencia = $repository_dependencia->customQuery(function ($model) use ($querySinproc) {
            return
                $model->where('codigo_homologado', $querySinproc[0]->numero04)->get();
        });

        //3. VALIDAR EL NOMBRE DE LA ENTIDAD INVOLUCRADA
        $repository_entidad = new RepositoryGeneric();
        $repository_entidad->setModel(new EntidadModel());
        $entidad = $repository_entidad->customQuery(function ($model) use ($querySinproc) {
            return
                $model->where('identidad', $querySinproc[0]->numero05)->get();
        });

        $reciboDatos['antecedente'] = $querySinproc[0]->texto08;
        $reciboDatos['dependencia_cargo'] = $querySinproc[0]->numero04;
        $reciboDatos['entidad_involucrada'] = $querySinproc[0]->numero05;

        if (count($dependencia) > 0) {
            $reciboDatos['nombre_dependencia_cargo'] = $dependencia[0]->nombre;
        } else {
            $error['estado'] = false;
            $error['error'] = 'No se encuentra registrada en base de datos la dependencia cargo.';
            return json_encode($error);
        }

        if (count($dependencia) > 0) {
            $reciboDatos['nombre_entidad_involucrada'] = $entidad[0]->nombre;
        } else {
            $error['estado'] = false;
            $error['error'] = 'No se encuentra registrada en base de datos la entidad involucrada.';
            return json_encode($error);
        }

        $json['data']['attributes'] = $reciboDatos;

        return json_encode($json);
    }

    /**
     *
     */
    public function validarDocumentoSINPROC($documento)
    {
        //$datosRequest = $request->validated();
        //INICIO PROCESO DE VALIDACION
        //1. Validación por SINPROC
        $repository_usuarioRol = new RepositoryGeneric();
        $repository_usuarioRol->setModel(new UsuarioRolModel());
        $querySinproc = $repository_usuarioRol->customQuery(function ($model) use ($documento) {
            return
                $model->where('cedula', $documento)
                ->get();
        });

        if (count($querySinproc) == 0) {
            $error['estado'] = false;
            $error['error'] = 'El número SINPROC y la vigencia ingresado no se encuentra registrado en la Base de Datos, verifiquelo y vuleva a intentar su validación.';
            return json_encode($error);
        }

        return json_encode($querySinproc);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProcesoDiciplinarioFormRequest $request) //: ProcesoDiciplinarioResource
    {
        try {

            DB::connection()->beginTransaction();
            $datosRecibidos = $request->validated()["data"]["attributes"];
            $datosRecibidos['created_user'] = auth()->user()->name;
            $datosRecibidos['radicado'] =  $datosRecibidos['radicado'];
            $datosRecibidos['vigencia_origen'] =  $request['vigencia'];
            $datosRecibidos['migrado'] =  false;
            $datosRecibidos['fuente_bd'] =  false;
            $datosRecibidos['fuente_excel'] =  false;


            $error = "";

            //validamos si el numero de radico es valido
            if ($datosRecibidos['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['correspondencia_sirius']) {
                $objetosirius = [];
                $objetosirius["radicado"] = $datosRecibidos['radicado'];
                $objetosirius["id_origen_radicado"] = $datosRecibidos['id_origen_radicado'];
                $objetosirius["id_tipo_proceso"] = $datosRecibidos['id_tipo_proceso'];
                $datosRecibidos['vigencia'] = $datosRecibidos['vigencia_origen'];

                $error = $this->validarProcesoSiriusLocal($objetosirius);
            } else if ($datosRecibidos['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['desglose']) {

                $objetoDesglose = [];
                $objetoDesglose["radicado"] = $datosRecibidos['radicado'];
                $objetoDesglose["id_origen_radicado"] = '';
                $objetoDesglose["id_tipo_proceso"] = $datosRecibidos['id_tipo_proceso'];
                $objetoDesglose["vigencia"] = $datosRecibidos['vigencia'];
            } else if ($datosRecibidos['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['sinproc']) {

                $objetoSinproc = [];
                $objetoSinproc["radicado"] =  $datosRecibidos['radicado'];
                $objetoSinproc["id_origen_radicado"] = "";
                $objetoSinproc["id_tipo_proceso"] = $datosRecibidos['id_tipo_proceso'];
                $objetoSinproc["vigencia"] = $datosRecibidos['vigencia'];

                $error = $this->validarProcesoSinprocLocal($objetoSinproc);
            } else if ($datosRecibidos['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['poder_preferente']) {

                $objetoPoderPreferente = [];
                $objetoPoderPreferente["radicado"] =  $datosRecibidos['radicado'];
                $objetoPoderPreferente["id_origen_radicado"] = "";
                $objetoPoderPreferente["id_tipo_proceso"] = $datosRecibidos['id_tipo_proceso'];
                $objetoPoderPreferente["vigencia"] = $datosRecibidos['vigencia'];
                $objetoPoderPreferente["entidad_involucrada"] = $datosRecibidos['entidad_involucrada'];
                $objetoPoderPreferente["dependencia_cargo"] = $datosRecibidos['dependencia_cargo'];
                $objetoPoderPreferente['id_etapa_asignada'] =  $datosRecibidos['id_etapa_asignada'];
                $datosRecibidos["id_dependencia_duena"] = $datosRecibidos['dependencia_cargo'];
                $datosRecibidos['id_dependencia'] =  $datosRecibidos['dependencia_cargo'];

                $error = $this->validarProcesoPreferente($objetoPoderPreferente);
            } else {
                throw new NotFoundHttpException('Proceso no encontrado.');
            }


            $obj = json_decode($error);
            if (!empty($obj->{'error'})) {
                // error_log('entro');
                return $error;
            }

            // error_log("error -> " . json_encode($error));

            $datosRecibidos['estado'] = true;

            // Se valida la dependencia en la que se encuentra el usuario en sesión
            $dependencia = DB::select("select id_dependencia from users where name = '" . $datosRecibidos['created_user'] . "'");
            // error_log("DEPENDENCIA: " . $dependencia[0]->id_dependencia);

            $datosRecibidos['id_dependencia'] = $dependencia[0]->id_dependencia;
            $datosRecibidos['id_dependencia_actual'] = $dependencia[0]->id_dependencia;

            //validar que no se inserten duplicados
            $countRadicados = ProcesoDiciplinarioModel::where([
                ['radicado', '=', $datosRecibidos['radicado']],
                ['vigencia_origen', '=', $datosRecibidos['vigencia']],
            ])->count();

            //error_log("countRadicados -> " . json_encode($countRadicados));

            //$datosRecibidos['id_funcionario_asignado'] =  $datosRecibidos['created_user'];

            if ($countRadicados > 0)
                throw new NotFoundHttpException('El radicado ' . $datosRecibidos['radicado'] . ' con la vigencia ' . $datosRecibidos['vigencia'] . ' ya se encuentra registrado en el sistema.');

            //dd($datosRecibidos);
            if ($datosRecibidos['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['sinproc']) {
                $datosRecibidos['vigencia_origen'] = $datosRecibidos['vigencia'];
                $datosRecibidos['vigencia'] = date("Y");
            } else if ($datosRecibidos['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['correspondencia_sirius']) {

                $temporal = [];
                $temporal["radicado"] = $datosRecibidos['radicado'];

                $datosRecibidos['radicado'] = substr($temporal['radicado'], 8, 7);
                if ($datosRecibidos['radicado'][0] == 0) {
                    $datosRecibidos['radicado'] = substr($datosRecibidos['radicado'], 1);
                }

                $datosRecibidos['tipo_radicacion'] = substr($temporal['radicado'], 5, 2);
                $datosRecibidos['vigencia_origen'] = substr($temporal['radicado'], 0, 4);
                $datosRecibidos['vigencia'] = $datosRecibidos['vigencia_origen'];
            }

            $respuesta = ProcesoDiciplinarioResource::make($this->repository->create($datosRecibidos));

            //error_log("respuesta -> " . json_encode($respuesta));

            $antecedente['descripcion'] = $datosRecibidos['antecedente'];
            if (!empty($datosRecibidos['id_dependencia_origen'])) {
                $antecedente['id_dependencia'] = $datosRecibidos['id_dependencia_origen'];
            } else {
                $antecedente['id_dependencia'] = auth()->user()->id_dependencia;
            }
            $antecedente['estado'] = true;
            $antecedente['id_proceso_disciplinario'] = $respuesta->resource->getRouteKey();
            $antecedente['id_etapa'] = 1;
            $antecedente['fecha_registro'] = Carbon::parse($datosRecibidos['fecha_ingreso'])->format('Y-m-d');

            if ($datosRecibidos['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['desglose']) {
                $antecedente['fecha_auto'] = Carbon::parse($datosRecibidos['fecha_auto_desglose'])->format('Y-m-d');
            }

            $datosRecibidos['id_proceso_disciplinario'] = $respuesta->resource->getRouteKey();

            $antecedente['created_user'] = auth()->user()->name;
            $uuid_antecedente = ProcesoDiciplinarioController::storeAntecedente($antecedente);


            if ($datosRecibidos['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['correspondencia_sirius']) {
                $datosRecibidos['uuid'] = $respuesta->resource->getRouteKey();
                $this->storeProcesoSirius($datosRecibidos);
            } else if ($datosRecibidos['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['desglose']) {
                $datosRecibidos['uuid'] = $respuesta->resource->getRouteKey();
                $this->storeProcesoDesglose($datosRecibidos);
            } else if ($datosRecibidos['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['sinproc']) {
                $datosRecibidos['uuid'] = $respuesta->resource->getRouteKey();
                $this->storeProcesoSinproc($datosRecibidos);
            } else if ($datosRecibidos['id_tipo_proceso'] == Constants::TIPO_DE_PROCESO['poder_preferente']) {
                $datosRecibidos['uuid'] = $respuesta->resource->getRouteKey();
                $this->storeProcesoPoderPreferente($datosRecibidos);
            } else {
                throw new NotFoundHttpException('Proceso no encontrado.');
            }

            $this->numeroCasosUsuario(auth()->user()->name);

            $logRequest['id_proceso_disciplinario'] = $antecedente['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = Constants::ETAPA['captura_reparto'];
            $logRequest['id_fase'] = Constants::FASE['antecedentes'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['etapa']; // Log de tipo Etapa
            $logRequest['descripcion'] = substr($antecedente['descripcion'], 0, 4000);
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['documentos'] = false; // No se adjuntan documentos
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['inicio_proceso_disciplinario'];
            //$logRequest['id_fase_registro'] = $datosRecibidos['uuid'];
            $logRequest['id_fase_registro'] = $uuid_antecedente;

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            DB::connection()->commit();
            return $respuesta;
        } catch (\Exception $e) {
            // Woopsy
            error_log($e);
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    public function validarProcesoDisciplinarioExistente($data)
    {
        $existe = false;

        // VALIDAR SI PUEDE ATENDER UN TIPO DE EXPEDIENTE DE QUEJA INTERNA
        $arrayProcesoDisciplinario = DB::select("select uuid from proceso_disciplinario where uuid = '" . $data["uuid"] . "'");

        // error_log("arrayProcesoDisciplinario -> " . json_encode($arrayProcesoDisciplinario));
        // error_log("count -> " . count($arrayProcesoDisciplinario));

        if (count($arrayProcesoDisciplinario) > 0) {
            $existe = true;
        }

        return $existe;
    }

    public function storeProcesoSirius($request)
    {
        $repository_sirius = new RepositoryGeneric();
        $repository_sirius->setModel(new ProcesoSiriusModel());
        $request['radicado_entidad'] = $request['radicado'];
        $request['fecha_ingreso'] = Carbon::parse($request['fecha_ingreso'])->format('Y-m-d');

        $respuesta = ProcesoSiriusResource::make($repository_sirius->create($request));

        // VALIDAR SI PUEDE ATENDER UN TIPO DE EXPEDIENTE DE QUEJA INTERNA
        $dependencia = DB::select("select id_dependencia_origen from mas_dependencia_configuracion where id_dependencia_acceso = 9 and id_dependencia_origen = " . auth()->user()->id_dependencia);

        if (count($dependencia) > 0) {

            /**********************************************
             * REGISTRAR CLASIFICACION DEL RADICADO
             **********************************************/

            $repository = new RepositoryGeneric();
            $repository->setModel(new ClasificacionRadicadoModel());

            $datosRequest['id_proceso_disciplinario'] = $request['uuid'];
            $datosRequest['id_etapa'] = LogTrait::etapaActual($request['uuid']);
            $datosRequest['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['queja'];
            $datosRequest['id_tipo_queja'] = Constants::TIPO_QUEJA['interna'];
            $datosRequest['estado'] = Constants::ESTADOS['activo'];
            $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;
            $datosRequest['observaciones'] = $request['antecedente'];
            $datosRequest['created_user'] = auth()->user()->name;

            $model = new ClasificacionRadicadoModel();
            $clasificacion = ClasificacionRadicadoResource::make($model->create($datosRequest));
            $array = json_decode(json_encode($clasificacion));

            // REGISTRA LA INFORMACIÓN EN EL LOG
            $datosRequest['id_fase'] = Constants::FASE['clasificacion_radicado'];
            $datosRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['clasificacion_expediente'];
            ClasificacionRadicadoController::storeLogClasificacionExpediente($datosRequest, auth()->user()->id_dependencia, auth()->user()->name, $array->id, false);

            // REGISTRAR ENTIDAD DEL INTERESADO
            $requestEntidad['id_proceso_disciplinario'] = $request['uuid'];
            $requestEntidad['id_etapa'] = LogTrait::etapaActual($request['uuid']);
            $requestEntidad['id_entidad'] = 67;
            $requestEntidad['requiere_registro'] = 1;
            $requestEntidad['estado'] = 1;
            $requestEntidad['observaciones'] = "NO_APLICA";
            $requestEntidad['cargo'] = "NO_APLICA";
            $requestEntidad['nombre_investigado'] = "NO_APLICA";
            $requestEntidad['created_user'] = auth()->user()->name;
            $model = new EntidadInvestigadoModel();
            $entidad = EntidadInvestigadoResource::make($model->create($requestEntidad));
            $array = json_decode(json_encode($entidad));

            // REGISTRA LA INFORMACIÓN EN EL LOG
            $datosRequest['id_fase'] = Constants::FASE['entidad_investigado'];
            $this->storeLog($datosRequest, auth()->user()->id_dependencia, auth()->user()->name,  $array->id, Constants::TIPO_DE_TRANSACCION['ninguno']);
        }

        return $respuesta;
    }


    public function storeProcesoDesglose($request)
    {
        $repository_desglose = new RepositoryGeneric();
        $repository_desglose->setModel(new ProcesoDesgloseModel());

        $request['id_tramite_usuario'] = $request['radicado'];
        $request['id_dependencia_origen'] = auth()->user()->id_dependencia;
        $request['id_dependencia_duena'] = $request['id_dependencia_origen'];
        $request['fecha_ingreso'] = Carbon::parse($request['fecha_ingreso'])->format('Y-m-d');
        $request['fecha_auto_desglose'] = Carbon::parse($request['fecha_auto_desglose'])->format('Y-m-d');

        /**********************************************
         * REGISTRAR CLASIFICACION DEL RADICADO
         **********************************************/

        $repository = new RepositoryGeneric();
        $repository->setModel(new ClasificacionRadicadoModel());

        // VALIDAR SI PUEDE ATENDER UN TIPO DE EXPEDIENTE DE QUEJA INTERNA
        $dependencia = DB::select("select id_dependencia_origen from mas_dependencia_configuracion where id_dependencia_acceso = 9 and id_dependencia_origen = " . auth()->user()->id_dependencia);

        if (count($dependencia) > 0) {
            $datosRequest['id_tipo_queja'] = Constants::TIPO_QUEJA['interna'];
        } else {
            $datosRequest['id_tipo_queja'] = Constants::TIPO_QUEJA['externa'];
        }

        $datosRequest['id_proceso_disciplinario'] = $request['uuid'];
        $datosRequest['id_etapa'] = LogTrait::etapaActual($request['uuid']);
        $datosRequest['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['proceso_disciplinario'];
        $datosRequest['estado'] = Constants::ESTADOS['activo'];
        $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;
        $datosRequest['observaciones'] = $request['antecedente'];
        $datosRequest['created_user'] = auth()->user()->name;

        $model = new ClasificacionRadicadoModel();
        $clasificacion = ClasificacionRadicadoResource::make($model->create($datosRequest));
        $array = json_decode(json_encode($clasificacion));

        // REGISTRA LA INFORMACIÓN EN EL LOG
        $datosRequest['id_fase'] = Constants::FASE['clasificacion_radicado'];
        $datosRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['clasificacion_expediente'];
        ClasificacionRadicadoController::storeLogClasificacionExpediente($datosRequest, auth()->user()->id_dependencia, auth()->user()->name, $array->id, false);

        /**********************************************
         * REGISTRAR VALIDACION DEL CLASIFICADO
         **********************************************/
        $datosValidacion['id_clasificacion_radicado'] = $array->id;
        $datosValidacion['created_user'] = auth()->user()->name;
        $datosValidacion['id_etapa'] = Constants::ETAPA['evaluacion'];
        $datosValidacion['estado'] = Constants::ESTADOS['activo'];
        $datosValidacion['id_proceso_disciplinario'] = $request['uuid'];
        $datosValidacion['eliminado'] = false;

        $modelValidarClasificacion = new ValidarClasificacionModel();
        $validacion = ValidarClasificacionResource::make($modelValidarClasificacion->create($datosValidacion));
        $array = json_decode(json_encode($validacion));

        //registramos log
        $logRequest['id_proceso_disciplinario'] = $request['uuid'];
        $logRequest['id_etapa'] = Constants::ETAPA['evaluacion'];
        $logRequest['id_fase'] = Constants::FASE['validacion_clasificacion'];
        $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
        $logRequest['descripcion'] = 'Se registra remisión validación del clasificado por defecto.';
        $logRequest['created_user'] = auth()->user()->name;
        $logRequest['id_estado'] = 3; // Remisionado
        $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia; // Remisionado
        $logRequest['documentos'] = false;
        $logRequest['id_fase_registro'] = $array->id;
        $logRequest['id_funcionario_actual'] = auth()->user()->name;
        $logRequest['id_funcionario_registra'] = auth()->user()->name;
        $logRequest['id_funcionario_asignado'] = auth()->user()->name;
        $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['ninguno'];

        $logModel = new LogProcesoDisciplinarioModel();
        LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

        return ProcesoDesgloseResource::make($repository_desglose->create($request));
    }

    public function storeProcesoSinproc($request)
    {
        $repository_sirius = new RepositoryGeneric();
        $repository_sirius->setModel(new ProcesoSinprocModel());
        //id_tramite_usuario -> es el mismo de radicacion
        $request['id_tramite_usuario'] = $request['radicado'];
        $request['fecha_ingreso'] = Carbon::parse($request['fecha_ingreso'])->format('Y-m-d');

        $respuesta =  ProcesoSinprocResource::make($repository_sirius->create($request));


        // VALIDAR SI PUEDE ATENDER UN TIPO DE EXPEDIENTE DE QUEJA INTERNA
        $dependencia = DB::select("select id_dependencia_origen from mas_dependencia_configuracion where id_dependencia_acceso = 9 and id_dependencia_origen = " . auth()->user()->id_dependencia);

        if (count($dependencia) > 0) {

            $this->updateIdDependenciaDuena(auth()->user()->id_dependencia,  $request['uuid']);
            $repository = new RepositoryGeneric();
            $repository->setModel(new ClasificacionRadicadoModel());

            $datosRequest['id_proceso_disciplinario'] = $request['uuid'];
            $datosRequest['id_etapa'] = LogTrait::etapaActual($request['uuid']);
            $datosRequest['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['queja'];
            $datosRequest['id_tipo_queja'] = Constants::TIPO_QUEJA['interna'];
            $datosRequest['estado'] = Constants::ESTADOS['activo'];
            $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;
            $datosRequest['observaciones'] = $request['antecedente'];
            $datosRequest['created_user'] = auth()->user()->name;

            /**********************************************
             * REGISTRAR CLASIFICACION DEL RADICADO
             **********************************************/

            $model = new ClasificacionRadicadoModel();
            $clasificacion = ClasificacionRadicadoResource::make($model->create($datosRequest));
            $array = json_decode(json_encode($clasificacion));

            // REGISTRA LA INFORMACIÓN EN EL LOG
            $datosRequest['id_fase'] = Constants::FASE['clasificacion_radicado'];
            $datosRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['clasificacion_expediente'];
            ClasificacionRadicadoController::storeLogClasificacionExpediente($datosRequest, auth()->user()->id_dependencia, auth()->user()->name, $array->id, false);

            // REGISTRAR ENTIDAD DEL INTERESADO
            $requestEntidad['id_proceso_disciplinario'] = $request['uuid'];
            $requestEntidad['id_etapa'] = LogTrait::etapaActual($request['uuid']);
            $requestEntidad['id_entidad'] = 67;
            $requestEntidad['requiere_registro'] = 1;
            $requestEntidad['estado'] = 1;
            $requestEntidad['observaciones'] = "NO_APLICA";
            $requestEntidad['cargo'] = "NO_APLICA";
            $requestEntidad['nombre_investigado'] = "NO_APLICA";
            $requestEntidad['created_user'] = auth()->user()->name;
            $model = new EntidadInvestigadoModel();
            $entidad = EntidadInvestigadoResource::make($model->create($requestEntidad));
            $array = json_decode(json_encode($entidad));

            // REGISTRA LA INFORMACIÓN EN EL LOG
            $datosRequest['id_fase'] = Constants::FASE['entidad_investigado'];
            $this->storeLog($datosRequest, auth()->user()->id_dependencia, auth()->user()->name,  $array->id, Constants::TIPO_DE_TRANSACCION['ninguno']);
        }

        return $respuesta;
    }


    public function storeProcesoPoderPreferente($request)
    {

        $repository = new RepositoryGeneric();
        $repository->setModel(new ProcesoPoderPreferenteModel());
        //id_tramite_usuario -> es el mismo de radicacion
        $request['id_tramite_usuario'] = $request['radicado'];
        $request['fecha_ingreso'] = Carbon::parse($request['fecha_ingreso'])->format('Y-m-d');
        $respuesta = ProcesoPoderPreferenteResource::make($repository->create($request));


        /**********************************************
         * REGISTRAR CLASIFICACION DEL RADICADO
         **********************************************/

        $repository = new RepositoryGeneric();
        $repository->setModel(new ClasificacionRadicadoModel());

        $datosRequest['id_proceso_disciplinario'] = $request['uuid'];
        $datosRequest['id_etapa'] = LogTrait::etapaActual($request['uuid']);
        $datosRequest['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['poder_referente'];
        $datosRequest['id_tipo_queja'] = Constants::TIPO_QUEJA['externa'];
        $datosRequest['estado'] = Constants::ESTADOS['activo'];
        $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;
        $datosRequest['observaciones'] = $request['antecedente'];
        $datosRequest['created_user'] = auth()->user()->name;

        $model = new ClasificacionRadicadoModel();
        $clasificacion = ClasificacionRadicadoResource::make($model->create($datosRequest));
        $array = json_decode(json_encode($clasificacion));

        // REGISTRA LA INFORMACIÓN EN EL LOG
        $datosRequest['id_fase'] = Constants::FASE['clasificacion_radicado'];
        $datosRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['clasificacion_expediente'];
        ClasificacionRadicadoController::storeLogClasificacionExpediente($datosRequest, auth()->user()->id_dependencia, auth()->user()->name, $array->id, false);

        // REGISTRAR ENTIDAD DEL INTERESADO
        $requestEntidad['id_proceso_disciplinario'] = $request['uuid'];
        $requestEntidad['id_etapa'] = LogTrait::etapaActual($request['uuid']);
        $requestEntidad['id_entidad'] = $request['entidad_involucrada'];
        $requestEntidad['requiere_registro'] = 1;
        $requestEntidad['estado'] = 1;
        $requestEntidad['observaciones'] = "NO_APLICA";
        $requestEntidad['cargo'] = "NO_APLICA";
        $requestEntidad['nombre_investigado'] = "NO_APLICA";
        $requestEntidad['created_user'] = auth()->user()->name;
        $model = new EntidadInvestigadoModel();
        $entidad = EntidadInvestigadoResource::make($model->create($requestEntidad));
        $array = json_decode(json_encode($entidad));

        // REGISTRA LA INFORMACIÓN EN EL LOG
        $datosRequest['id_fase'] = Constants::FASE['entidad_investigado'];
        $this->storeLog($datosRequest, auth()->user()->id_dependencia, auth()->user()->name,  $array->id, Constants::TIPO_DE_TRANSACCION['ninguno']);


        /**********************************************
         * REGISTRAR REMISION QUEJA
         **********************************************/

        // REGISTRAR ENTIDAD DEL INTERESADO
        /*$requestRemisionQueja['id_proceso_disciplinario'] = $request['uuid'];
        $requestRemisionQueja['id_tipo_evaluacion'] = Constants::RESULTADO_EVALUACION['comisorio_eje'];
        $requestRemisionQueja['id_dependencia_origen'] = auth()->user()->id_dependencia;
        $requestRemisionQueja['id_dependencia_destino'] = $request['dependencia_cargo'];
        $requestRemisionQueja['eliminado'] = false;

        $model = new RemisionQuejaModel();
        $remision_queja = RemisionQuejaResource::make($model->create($requestRemisionQueja));
        $array = json_decode(json_encode($remision_queja));

        // REGISTRA LA INFORMACIÓN EN EL LOG
        $datosRequest['id_fase'] = Constants::FASE['remision_queja'];
        $this->storeLog($datosRequest, auth()->user()->id_dependencia, auth()->user()->name,  $array->id, Constants::TIPO_DE_TRANSACCION['ninguno']);
*/
        return $respuesta;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProcesoDiciplinario  $procesoDiciplinario
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ProcesoDiciplinarioResource::make($this->repository->find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProcesoDiciplinario  $procesoDiciplinario
     * @return \Illuminate\Http\Response
     */
    public function edit(ProcesoDiciplinarioModel $procesoDiciplinario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProcesoDiciplinario  $procesoDiciplinario
     * @return \Illuminate\Http\Response
     */
    public function update(ProcesoDiciplinarioRemisionFormRequest $request,  $id)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRecibidos = $request->validated()["data"]["attributes"];

            //generamos log
            $logRequest['id_proceso_disciplinario'] = $datosRecibidos['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = 1;
            $logRequest['id_fase'] = 0; // No hay fase inicial
            $logRequest['id_tipo_log'] = 1; // Log de tipo Etapa
            $logRequest['documentos'] = false;
            $logRequest['descripcion'] = substr($datosRecibidos['descripcion_a_remitir'], 0, 4000);
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['id_dependencia_origen'] = $datosRecibidos['id_dependencia_origen'];
            $logRequest['id_funcionario_actual'] = $datosRecibidos['usuario_a_remitir'];
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = $datosRecibidos['usuario_a_remitir'];
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['ninguno'];
            ProcesoDiciplinarioController::removerFuncionarioActualLog($datosRecibidos['id_proceso_disciplinario']);

            $usuario_remitir = User::where('name', $datosRecibidos['usuario_a_remitir'])->get();
            ProcesoDiciplinarioModel::where('uuid', $datosRecibidos['id_proceso_disciplinario'])->update(['id_dependencia_actual' => $usuario_remitir[0]->id_dependencia]);

            $logModel = new LogProcesoDisciplinarioModel();
            $respuesta = LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

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

    public function updateUsuarioComisionado($id_usuario_comisionado,  $id_proceso_disciplinario)
    {
        try {
            DB::connection()->beginTransaction();

            $respuesta = ProcesoDiciplinarioModel::where('uuid', $id_proceso_disciplinario)
                ->update(['usuario_comisionado' => $id_usuario_comisionado]);

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

    public function updateIdDependenciaDuena($id_dependencia_duena,  $id_proceso_disciplinario)
    {
        return ProcesoDiciplinarioModel::where('uuid', $id_proceso_disciplinario)
            ->update(['id_dependencia_duena' => $id_dependencia_duena]);
    }

    public function updateEstadoProcesoPorActuacion($id_proceso_disciplinario)
    {
        return ProcesoDiciplinarioModel::where('uuid', $id_proceso_disciplinario)->update(['estado' => 3]);
    }

    /**
     * Traslado masivo de casos
     */
    public function trasladoMasivoCasos(TrasladoMasivoFormRequest $request)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRecibidos = $request->validated()["data"]["attributes"];
            $respuesta = '';

            $user = User::where("name", $datosRecibidos['usuario_a_remitir'])->first();
            // error_log(json_encode($user));

            //si el usuario puede recibir expedientes continuamos
            if ($user->reparto_habilitado == true) {

                //ACUTALIZAMOS LOS TIPOS DE EXPEDIENTE

                $numero_casos = 0;

                if ($datosRecibidos["lista_procesos"]) {

                    foreach ($datosRecibidos["lista_procesos"] as $procesoDisciplinario) {

                        //validamos si cada proceso tiene registro en clasificación, con esto sabremos si
                        // el usuario destino lo puede recibir
                        $respuestaClasificacion = ClasificacionRadicadoModel::where("id_proceso_disciplinario", $procesoDisciplinario)
                            ->where("estado", Constants::ESTADOS['activo'])->first();

                        if (!$respuestaClasificacion) {

                            $proceso_disciplinario = ProcesoDiciplinarioModel::where("uuid", $procesoDisciplinario)->get();
                            $error['estado'] = false;
                            $error['error'] = 'El proceso ' . $proceso_disciplinario[0]->radicado . ' no tiene tipo de expediente registrado todavía, sin esta información no se puede asignar a un usuario';
                            return json_encode($error);
                        } else {

                            //buscamos que expedientes puede realizar el usuario
                            $siRecibe = false;
                            $queryUser = DB::select(DB::raw("select user_id, tipo_expediente_id, sub_tipo_expediente_id from users_tipo_expediente e where e.user_id = :userId"), array('userId' => $user->id,));


                            // Se valida la etapa en la que se encuentra el usuario
                            $evaluacion_cerrada = $this->getEstadoFaseProcesoDisciplinario($procesoDisciplinario, Constants::FASE['cierre_evaluacion']);

                            if ($evaluacion_cerrada) {
                                $siRecibe = true;
                            } else {

                                if (!empty($queryUser[0])) {

                                    foreach ($queryUser as $r) {

                                        //validamos si el usuario recibe esos tipos de expedientes
                                        $tipoExpediente = $respuestaClasificacion->id_tipo_expediente;
                                        $idTipoqueja = $respuestaClasificacion->id_tipo_queja;
                                        $idTerminoRespuesta = $respuestaClasificacion->id_termino_respuesta;
                                        $idTipoDerechoPeticion = $respuestaClasificacion->id_tipo_derecho_peticion;

                                        if ((($tipoExpediente . '|' . $idTipoqueja) == ($r->tipo_expediente_id . '|' . $r->sub_tipo_expediente_id))
                                            || (($tipoExpediente . '|' . $idTipoqueja) == ($r->tipo_expediente_id . '|' . $r->sub_tipo_expediente_id))
                                            || (($tipoExpediente . '|' . $idTerminoRespuesta) == ($r->tipo_expediente_id . '|' . $r->sub_tipo_expediente_id))
                                            || (($tipoExpediente . '|' . $idTipoDerechoPeticion) == ($r->tipo_expediente_id . '|' . $r->sub_tipo_expediente_id))
                                        ) {
                                            $siRecibe = true;
                                        }
                                    }
                                } else {
                                    $error['estado'] = false;
                                    $error['error'] = 'El usuario destino ' . $datosRecibidos['usuario_a_remitir'] . ' no tiene tipos de expedientes asociados para recibir casos';
                                    return json_encode($error);
                                }
                            }

                            if ($siRecibe) {
                                //error_log('super');
                                $numero_casos++;

                                LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $procesoDisciplinario)->update(['id_funcionario_actual' => null]);

                                $logModel = new LogProcesoDisciplinarioModel();
                                $logRequest['id_proceso_disciplinario'] = $procesoDisciplinario;
                                $logRequest['id_etapa'] = 1;
                                $logRequest['id_fase'] = 0; // No hay fase inicial
                                $logRequest['id_tipo_log'] = 1; // Log de tipo Etapa
                                $logRequest['descripcion'] = 'Proceso reasignado';
                                $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido']; // Remisionado
                                $logRequest['id_dependencia_origen'] = $user["id_dependencia"];
                                $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['reclasificacion_expediente'];
                                $logRequest['created_user'] = auth()->user()->name;
                                $logRequest['id_funcionario_actual'] = $datosRecibidos['usuario_a_remitir'];
                                $logRequest['id_funcionario_asignado'] =  $datosRecibidos['usuario_a_remitir'];
                                $logRequest['id_funcionario_registra'] = auth()->user()->name;
                                $respuesta = LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
                            } else {
                                $error['estado'] = false;
                                $error['error'] = 'El usuario destino ' . $datosRecibidos['usuario_a_remitir'] . ' no puede recibir uno a más de estos casos, revise
                                    cuales expedientes tiene habilitado el usuario o que tipo de expedientes esta intentando reasignar';
                                return json_encode($error);
                            }
                        }
                    }
                }
            } else {
                $error['estado'] = false;
                $error['error'] = 'El usuario destino ' . $datosRecibidos['usuario_a_remitir'] . ' no esta habilitado para recibir casos';
                return json_encode($error);
            }

            $this->numeroCasosUsuario($datosRecibidos['usuario_a_remitir'], $numero_casos);


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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProcesoDiciplinario  $procesoDiciplinario
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->noContent();
    }

    /**
     * OBTENER TIPO DE PROCESO DISCIPLINARIO
     */
    public function getTipoProcesoDisciplinario($id_proceso_disciplinario)
    {

        // Se valida la dependencia en la que se encuentra el usuario en sesión
        $proceso_disciplinario = DB::select("select id_tipo_proceso from proceso_disciplinario where uuid = '" . $id_proceso_disciplinario . "'");

        $reciboDatos['id_tipo_proceso'] = $proceso_disciplinario[0]->id_tipo_proceso;
        $json['data']['attributes'] = $reciboDatos;

        return json_encode($json);
    }

    /**
     * Metodo encargado de llamar el metodo para validar el proceso sinproc
     */
    public function validarProcesoSinprocPortalWeb(ProcesoSinprocFormRequest $request)
    {
        // Se captura la informacion del form
        $datosRequest = $request->validated()["data"]["attributes"];

        // Se llama y retorna el metodo encargado de validar el proceso sinproc
        return $this->validarProcesosSinprocPortalWeb($datosRequest);
    }


    public function validarProcesosSinprocPortalWeb(array $datosRequest)
    {
        // Se inicializan las clases y el modelo


        // Existe en tramiteusuario
        $querySinproc = $this->repository->customQuery(function ($model) use ($datosRequest) {
            return
                $model->where('radicado', $datosRequest['radicado'])->where('vigencia', $datosRequest['vigencia'])->get();
        });


        error_log(json_encode($querySinproc));


        // Se valida cuando no existe el proceso
        if (empty($querySinproc[0])) {

            // Se setean los valores al array
            $error['estado'] = false;
            $error['error'] = 'El número SINPROC ' . $datosRequest['radicado'] . ' con vigencia ' . $datosRequest['vigencia'] . ' no se encuentra registrado en la base de datos, verifíquelo y vuelva a intentar su validación.';

            // Se retorna
            return json_encode($error);
        }

        // Se setean los valores al array
        //$reciboDatos['antecedente'] = $querySinproc[0]->texto08;
        $json['data']['attributes'] = $querySinproc;

        // Se retorna
        return json_encode($json);
    }


    public function contadorDesgloses($sinproc)
    {

        $query = $this->repository->customQuery(function ($model) use ($sinproc) {
            return $model
                ->where('radicado_padre', $sinproc)
                ->get();
        });

        return count($query);
    }

    public function getInfoHijos($sinproc)
    {

        $arr = array();

        $query = $this->repository->customQuery(function ($model) use ($sinproc) {
            return $model
                ->where('radicado_padre', $sinproc)
                ->get();
        });

        foreach ($query as $key => $value) {

            $this->repository->setModel(new AntecedenteModel());
            $queryAntecendente = $this->repository->customQuery(function ($model) use ($value) {
                return $model->where('id_proceso_disciplinario', $value->uuid)->get();
            })->first();

            $this->repository->setModel(new DependenciaOrigenModel());
            $queryDependencia = $this->repository->customQuery(function ($model) use ($value) {
                return $model->where('id', $value->id_dependencia)->get();
            })->first();

            array_push(
                $arr,
                array(
                    "type" => "desglose",
                    "attributes" => array(
                        "radicado" => $value->radicado,
                        "fecha_registro" => date("d/m/Y h:i:s A", strtotime($value->created_at)),
                        "registrado_por" => $value->created_user,
                        "dependencia_solicitante" => $queryDependencia->nombre,
                        "antecedente" => $queryAntecendente->descripcion,
                    )
                )
            );
        }

        $rtaFinal = array(
            "data" => $arr
        );

        return json_encode($rtaFinal);
    }


    public function getInfoHijo($id_proceso_disciplinario)
    {
        $arr = array();

        $this->repository->setModel(new ProcesoDesgloseModel());
        $query = $this->repository->customQuery(function ($model) use ($id_proceso_disciplinario) {
            return $model
                ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
                ->get();
        })->first();
        error_log(json_encode($query));
        $dependenciaOrigen = new DependenciaOrigenModel();
        array_push(
            $arr,
            array(
                "type" => "desglose",
                "attributes" => array(
                    "id_tramite_usuario" => $query->id_tramite_usuario,
                    "fecha_ingreso" => date("d/m/Y h:i:s A", strtotime($query->fecha_ingreso)),
                    "numero_auto" => $query->numero_auto,
                    "auto_asociado" => $query->auto_asociado,
                    "fecha_auto_desglose" => date("d/m/Y h:i:s A", strtotime($query->fecha_auto_desglose)),
                    "id_dependencia_origen" =>  DependenciaOrigenResource::make($dependenciaOrigen->find($query->id_dependencia_origen)),
                    "id_proceso_disciplinario" => $query->id_proceso_disciplinario,
                    "created_user" => $query->created_user,
                    "updated_user" => $query->updated_user,
                    "deleted_user" => $query->deleted_user,
                )
            )
        );

        $rtaFinal = array(
            "data" => $arr
        );

        return json_encode($rtaFinal);
    }

    public function getProcesoPorRadicado($radicado)
    {
        $query = $this->repository->customQuery(function ($model) use ($radicado) {
            return $model
                ->where('radicado', $radicado)
                ->get();
        })->first();

        return ProcesoDiciplinarioResource::make($this->repository->find($query->uuid));
    }

    public function establecerVigencia($id_proceso_disciplinario, $vigencia)
    {
        try {
            DB::connection()->beginTransaction();

            ProcesoDiciplinarioModel::where('uuid', $id_proceso_disciplinario)->update(['vigencia' => $vigencia, 'vigencia_origen' => $vigencia]);

            DB::connection()->commit();
            return true;
        } catch (\Exception $e) {
            // Woopsy
            error_log($e);
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    public function usuarioHabilitadoParaTransacciones($id_proceso_disciplinario, $id_usuario)
    {
        try {

            $user = DB::select(
                "
                    SELECT
                        u.id,
                        u.nombre,
                        u.apellido,
                        u.reparto_habilitado,
                        u.estado,
                        ute.tipo_expediente_id,
                        ute.sub_tipo_expediente_id
                    FROM
                        users u
                    INNER JOIN users_tipo_expediente ute ON u.id = ute.user_id
                    WHERE u.id = $id_usuario
                "
            );

            if (count($user) <= 0) {
                $error['estado'] = false;
                $error['error'] = 'EL USUARIO ANTERIOR NO TIENE PERMISOS PARA RECIBIR EL PROCESO DISCIPLINARIO.';
                return json_encode($error);
            } else if (!$user[0]->reparto_habilitado || !$user[0]->estado) {
                $error['estado'] = false;
                $error['error'] = 'EL USUARIO NO ESTÁ ACTIVO Y/O NO ESTÁ HABILITADO PARA REPARTO.';
                return json_encode($error);
            } else {

                $permisoUsurio = false;

                foreach ($user as $permisos) {
                    if ($permisos->tipo_expediente_id === Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {
                        $permisoUsurio = true;
                        break;
                    }
                }

                if ($permisoUsurio) {
                    $error['estado'] = false;
                    $error['error'] = 'EL USUARIO ' . strtoupper($user[0]->nombre) . ' ' . strtoupper($user[0]->apellido) . ' NO TIENE LOS PERMISOS NECESARIOS PARA REALIZAR EL REPARTO.';
                    return json_encode($error);
                }
            }

            $proceso_disciplinario = DB::select(
                "
                    SELECT
                        cr.id_tipo_expediente,
                        cr.id_tipo_queja,
                        cr.created_at
                    FROM
                    proceso_disciplinario pd
                    INNER JOIN clasificacion_radicado cr ON pd.uuid = cr.id_proceso_disciplinario
                    WHERE pd.uuid = '$id_proceso_disciplinario'
                    ORDER BY cr.created_at DESC
                "
            );

            if (count($proceso_disciplinario) <= 0) {
                $error['estado'] = false;
                $error['error'] = 'ERROR AL MOMENTO DE OBTENER INFORMACIÓN DEL PROCESO.';
                return json_encode($error);
            }

            foreach ($user as $permiso_expedientes) {
                if (
                    $permiso_expedientes->tipo_expediente_id == Constants::TIPO_EXPEDIENTE['proceso_disciplinario'] &&
                    $permiso_expedientes->sub_tipo_expediente_id == $proceso_disciplinario[0]->id_tipo_queja
                ) {
                    return true;
                }
            }

            $error['estado'] = false;
            $error['error'] = 'EL USUARIO ' . strtoupper($user[0]->nombre) . ' ' . strtoupper($user[0]->apellido) . ' NO TIENE PERMISOS PARA RECIBIR EL PROCESO DISCIPLINARIO.';
            return json_encode($error);
        } catch (\Exception $e) {
            // Woopsy
            error_log($e);
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }


    public function encabezadoDelProceso($id_proceso_disciplinario)
    {

        $proceso_disciplinario = DB::select("SELECT
                pd.uuid AS id_proceso_disciplinario,
                pd.radicado AS radicado,
                pd.vigencia AS vigencia,
                pd.id_tipo_proceso AS id_tipo_proceso,
                mtp.nombre AS nombre_tipo_proceso,
                pd.id_dependencia AS id_dependencia_registro,
                md_origen.nombre AS nombre_dependencia_registro,
                pd.id_dependencia_duena AS id_dependencia_duena,
                md_duena.nombre AS nombre_dependencia_duena,
                pd.id_dependencia_duena AS id_dependencia_actual,
                md_actual.nombre AS nombre_dependencia_actual,
                pd.created_user AS id_funcionario_registro,
                UPPER(CONCAT(CONCAT(u_registro.nombre, ' '), u_registro.apellido)) AS nombre_funcionario_registro,
                pd.usuario_comisionado AS id_usuario_comisionado,
                UPPER(CONCAT(CONCAT(u_comisionado.nombre, ' '), u_comisionado.apellido)) AS nombre_usuario_comisionado,
                FROM proceso_disciplinario pd
                INNER JOIN mas_tipo_proceso mtp ON mtp.id = pd.id_tipo_proceso
                INNER JOIN mas_dependencia_origen md_origen ON md_origen.id = pd.id_dependencia
                INNER JOIN mas_dependencia_origen md_duena ON md_duena.id = pd.id_dependencia_duena
                INNER JOIN mas_dependencia_origen md_actual ON md_actual.id = pd.id_dependencia_actual
                INNER JOIN users u_registro ON u_registro.name = pd.created_user
                INNER JOIN users u_comisionado ON u_comisionado.id = pd.usuario_comisionado
                WHERE uuid = '" . $id_proceso_disciplinario . "'");

        $ultimo_antecedente = DB::select("SELECT
            a.descripcion AS descripcion,
            a.fecha_registro AS fecha_registro
            FROM antecedente a
            WHERE id_proceso_disciplinario = '" . $proceso_disciplinario[0]->ID_PROCESO_DISCIPLINARIO . "' AND estado = 1
            ORDER BY a.fecha_registro DESC");


        $array = array();

        $reciboDatos['attributes']['id'] = $datos_filtrados[$cont]->id;
        $reciboDatos['attributes']['radicado'] = $datos_filtrados[$cont]->radicado;
        $reciboDatos['attributes']['vigencia'] = $datos_filtrados[$cont]->vigencia;
        $reciboDatos['attributes']['fecha'] = Utilidades::getFormatoFechaDDMMYY($datos_filtrados[$cont]->fecha);
        $reciboDatos['attributes']['estado_expediente'] = $datos_filtrados[$cont]->estado;
        $reciboDatos['attributes']['dependencia'] = $datos_filtrados[$cont]->dependencia;
        $reciboDatos['attributes']['etapa'] = $datos_filtrados[$cont]->etapa;
        $reciboDatos['attributes']['antecedente'] = $datos_filtrados[$cont]->descripcion;
        $reciboDatos['attributes']['fecha_antecedente'] = Utilidades::getFormatoFechaDDMMYY($datos_filtrados[$cont]->fecha_antecedente);
        $reciboDatos['attributes']['antecedente_corto'] = Utilidades::getDescripcionCorta($datos_filtrados[$cont]->descripcion);
        $reciboDatos['attributes']['funcionario_actual'] = $datos_filtrados[$cont]->funcionario_actual;
    }
}
