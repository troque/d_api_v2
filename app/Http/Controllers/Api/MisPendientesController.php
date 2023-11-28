<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MisPendientesFormRequest;
use App\Http\Resources\MisPendientes\MisPendientesCollection;
use App\Http\Utilidades\Constants;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\TipoQuejaModel;
use App\Models\TipoDerechoPeticionModel;
use App\Models\TerminoRespuestaModel;
use App\Models\TipoExpedienteModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MisPendientesController extends Controller
{
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
    }

    private function obtenerFilas($opcion, $datosRequest){

        if ($opcion == 1) {
            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model
                    ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                    ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                    ->whereDate('log_proceso_disciplinario.created_at', '=',  $datosRequest['fecha'])
                    ->where('proceso_disciplinario.estado', Constants::ESTADO_PROCESO_DISCIPLINARIO['activo'])
                    ->latest('log_proceso_disciplinario.created_at')
                    ->count();
            });
        }
        else if ($opcion == 2) {
            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model
                    ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                    ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                    ->whereDate('log_proceso_disciplinario.created_at', '>=',  $datosRequest['fecha'])
                    ->where('proceso_disciplinario.estado', Constants::ESTADO_PROCESO_DISCIPLINARIO['activo'])
                    ->latest('log_proceso_disciplinario.created_at')
                    ->count();
            });
        }
        else if ($opcion == 3) {
            $query = $this->repository->customQuery(function ($model){
                return $model
                    ->distinct()
                    ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                    ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                    ->where('proceso_disciplinario.estado', Constants::ESTADO_PROCESO_DISCIPLINARIO['activo'])
                    ->latest('log_proceso_disciplinario.created_at')
                    ->count();
            });
        }
        else if ($opcion == 4){
            $query = $this->repository->customQuery(function ($model) use ($datosRequest){
                return $model
                    ->distinct()
                    ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                    ->leftJoin('mas_etapa', 'mas_etapa.id', '=', 'proceso_disciplinario.id_etapa')
                    ->leftJoin('mas_tipo_proceso', 'mas_tipo_proceso.id', '=', 'proceso_disciplinario.id_tipo_proceso')
                    ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                    ->where('proceso_disciplinario.estado', Constants::ESTADO_PROCESO_DISCIPLINARIO['activo'])
                    ->latest('log_proceso_disciplinario.created_at')
                    ->where(function ($query) use ($datosRequest) {
                        $searchTerm = $datosRequest['palabra_buscar']; // Término de búsqueda
                        $query->where('proceso_disciplinario.uuid', 'LIKE', "%$searchTerm%")
                            ->orWhere('proceso_disciplinario.radicado', 'LIKE', "%$searchTerm%")
                            ->orWhere('proceso_disciplinario.vigencia', 'LIKE', "%$searchTerm%")
                            ->orWhere('proceso_disciplinario.id_tipo_proceso', 'LIKE', "%$searchTerm%")
                            ->orWhere('proceso_disciplinario.id_etapa', 'LIKE', "%$searchTerm%")
                            ->orWhere('log_proceso_disciplinario.created_at', 'LIKE', "%$searchTerm%")
                            ->orWhere('mas_etapa.nombre', 'like', '%' . $searchTerm . '%')
                            ->orWhere('mas_tipo_proceso.nombre', 'like', '%' . $searchTerm . '%');
                    })
                    ->count();
            });
        }
        
        return $query;

    }

    public function getMisPendientesFilter(MisPendientesFormRequest $request)
    {

        $datosRequest = $request->validated()["data"]["attributes"];

        $perPage = $datosRequest['per_page']; // Número de resultados por página
        $page = $datosRequest['current_page']; // Número de página actual
        $totalResults = 0;
        $totalPages = 0;

        if(!empty($datosRequest['palabra_buscar'])){
            $totalResults = $this->obtenerFilas(4,$datosRequest);
            $totalPages = ceil($totalResults / $perPage);

            $query = $this->repository->customQuery(function ($model) use ($datosRequest, $perPage, $page) {
                return $model->select(
                        'proceso_disciplinario.uuid',
                        'proceso_disciplinario.radicado',
                        'proceso_disciplinario.vigencia',
                        'proceso_disciplinario.id_tipo_proceso',
                        'proceso_disciplinario.estado',
                        'proceso_disciplinario.id_etapa',
                        'proceso_disciplinario.migrado',
                        'proceso_disciplinario.created_at',
                        'log_proceso_disciplinario.created_at AS fecha_actualizacion',
                        'log_proceso_disciplinario.descripcion AS ultima_descripcion_log'
                    )->distinct()
                    ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                    ->leftJoin('mas_etapa', 'mas_etapa.id', '=', 'proceso_disciplinario.id_etapa')
                    ->leftJoin('mas_tipo_proceso', 'mas_tipo_proceso.id', '=', 'proceso_disciplinario.id_tipo_proceso')
                    ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                    ->where('proceso_disciplinario.estado', Constants::ESTADO_PROCESO_DISCIPLINARIO['activo'])
                    ->latest('log_proceso_disciplinario.created_at')
                    ->where(function ($query) use ($datosRequest) {
                        $searchTerm = $datosRequest['palabra_buscar']; // Término de búsqueda
                        $query->where('proceso_disciplinario.uuid', 'LIKE', "%$searchTerm%")
                            ->orWhere('proceso_disciplinario.radicado', 'LIKE', "%$searchTerm%")
                            ->orWhere('proceso_disciplinario.vigencia', 'LIKE', "%$searchTerm%")
                            ->orWhere('proceso_disciplinario.id_tipo_proceso', 'LIKE', "%$searchTerm%")
                            ->orWhere('proceso_disciplinario.id_etapa', 'LIKE', "%$searchTerm%")
                            ->orWhere('log_proceso_disciplinario.created_at', 'LIKE', "%$searchTerm%")
                            ->orWhere('mas_etapa.nombre', 'like', '%' . $searchTerm . '%')
                            ->orWhere('mas_tipo_proceso.nombre', 'like', '%' . $searchTerm . '%');
                    })
                    ->offset(($page - 1) * $perPage) // Calcula el desplazamiento basado en el número de página
                    ->limit($perPage)
                    ->get();
            });
        }
        else if(!empty($datosRequest['fecha'])) {

            if ($datosRequest['fecha'] == date('Y-m-d')) {

                $totalResults = $this->obtenerFilas(1,$datosRequest);
                $totalPages = ceil($totalResults / $perPage);

                $query = $this->repository->customQuery(function ($model) use ($datosRequest, $perPage, $page) {
                    return $model->select(
                        'proceso_disciplinario.uuid',
                        'proceso_disciplinario.radicado',
                        'proceso_disciplinario.vigencia',
                        'proceso_disciplinario.id_tipo_proceso',
                        'proceso_disciplinario.estado',
                        'proceso_disciplinario.id_etapa',
                        'proceso_disciplinario.migrado',
                        'proceso_disciplinario.created_at',
                        'log_proceso_disciplinario.created_at AS fecha_actualizacion',
                        'log_proceso_disciplinario.descripcion AS ultima_descripcion_log'
                    )->orderBy('log_proceso_disciplinario.created_at', 'desc')
                        ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                        ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                        ->whereDate('proceso_disciplinario.updated_at', '=',  $datosRequest['fecha'])
                        ->where('proceso_disciplinario.estado', Constants::ESTADO_PROCESO_DISCIPLINARIO['activo'])
                        ->offset(($page - 1) * $perPage) // Calcula el desplazamiento basado en el número de página
                        ->limit($perPage)
                        ->get();
                });
            } else {

                $totalResults = $this->obtenerFilas(2,$datosRequest);
                $totalPages = ceil($totalResults / $perPage);

                $query = $this->repository->customQuery(function ($model) use ($datosRequest, $perPage, $page) {
                    return $model->select(
                        'proceso_disciplinario.uuid',
                        'proceso_disciplinario.radicado',
                        'proceso_disciplinario.vigencia',
                        'proceso_disciplinario.id_tipo_proceso',
                        'proceso_disciplinario.estado',
                        'proceso_disciplinario.id_etapa',
                        'proceso_disciplinario.migrado',
                        'proceso_disciplinario.created_at',
                        'log_proceso_disciplinario.created_at AS fecha_actualizacion',
                        'log_proceso_disciplinario.descripcion AS ultima_descripcion_log'
                    )->orderBy('log_proceso_disciplinario.created_at', 'desc')
                        ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                        ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                        ->whereDate('proceso_disciplinario.updated_at', '>=',  $datosRequest['fecha'])
                        ->where('proceso_disciplinario.estado', Constants::ESTADO_PROCESO_DISCIPLINARIO['activo'])
                        ->offset(($page - 1) * $perPage) // Calcula el desplazamiento basado en el número de página
                        ->limit($perPage)
                        ->get();
                });
            }
        } else {

            $totalResults = $this->obtenerFilas(3,$datosRequest);
            $totalPages = ceil($totalResults / $perPage);

            $query = $this->repository->customQuery(function ($model) use ($perPage, $page) {
                return $model->select(
                    'proceso_disciplinario.uuid',
                    'proceso_disciplinario.radicado',
                    'proceso_disciplinario.vigencia',
                    'proceso_disciplinario.id_tipo_proceso',
                    'proceso_disciplinario.estado',
                    'proceso_disciplinario.id_etapa',
                    'proceso_disciplinario.migrado',
                    'proceso_disciplinario.created_at',
                    'log_proceso_disciplinario.created_at AS fecha_actualizacion',
                    'log_proceso_disciplinario.descripcion AS ultima_descripcion_log'
                )->distinct()
                    ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                    ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                    ->where('proceso_disciplinario.estado', Constants::ESTADO_PROCESO_DISCIPLINARIO['activo'])
                    ->latest('log_proceso_disciplinario.created_at')
                    ->offset(($page - 1) * $perPage) // Calcula el desplazamiento basado en el número de página
                    ->limit($perPage)
                    ->get();
            });
        }


        $arr = array();
        foreach (MisPendientesCollection::make($query) as $key => $value) {
            //error_log(MisPendientesCollection::make($query)[$key]->getTipoExpediente);

            if (MisPendientesCollection::make($query)[$key]->getTipoExpediente) {

                if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 1 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_derecho_peticion == 1) {
                    $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 1)->get()->first();
                    $nombre2 = TipoDerechoPeticionModel::query()->select('mas_tipo_derecho_peticion.nombre')->where('id', '=', 1)->get()->first();
                    $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 1 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_derecho_peticion == 2) {
                    $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 1)->get()->first();
                    $nombre2 = TipoDerechoPeticionModel::query()->select('mas_tipo_derecho_peticion.nombre')->where('id', '=', 2)->get()->first();
                    $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 1 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_derecho_peticion == 3) {
                    $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 1)->get()->first();
                    $nombre2 = TipoDerechoPeticionModel::query()->select('mas_tipo_derecho_peticion.nombre')->where('id', '=', 3)->get()->first();
                    $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 2 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_queja == 1) {
                    $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 2)->get()->first();
                    $nombre2 = TipoQuejaModel::query()->select('mas_tipo_queja.nombre')->where('id', '=', 1)->get()->first();
                    $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 3 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_queja == 1) {
                    $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 3)->get()->first();
                    $nombre2 = TipoQuejaModel::query()->select('mas_tipo_queja.nombre')->where('id', '=', 1)->get()->first();
                    $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 3 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_queja == 2) {
                    $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 3)->get()->first();
                    $nombre2 = TipoQuejaModel::query()->select('mas_tipo_queja.nombre')->where('id', '=', 2)->get()->first();
                    $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 4 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_termino_respuesta == 1) {
                    $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 4)->get()->first();
                    $nombre2 = TerminoRespuestaModel::query()->select('mas_termino_respuesta.nombre')->where('id', '=', 1)->get()->first();
                    $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 4 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_termino_respuesta == 2) {
                    $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 4)->get()->first();
                    $nombre2 = TerminoRespuestaModel::query()->select('mas_termino_respuesta.nombre')->where('id', '=', 2)->get()->first();
                    $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 5) {
                    $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 5)->get()->first();
                    $query2 = array("nombre" => $nombre->nombre);
                } else {
                    $query2 = array("nombre" => "Sin Clasificación");
                }
            } else {
                $query2 = array("nombre" => "Sin Clasificación");
            }

            array_push(
                $arr,
                array(
                    "type" => "proceso_disciplinario",
                    "attributes" => array(
                        "MisPendientes" => MisPendientesCollection::make($query)[$key],
                        "Clasificacion" => $query2,
                        "TotalPaginas" => $totalPages,
                        "TotalRegistros" => $totalResults
                    )
                )
            );
        }
        $rtaFinal = array(
            "data" => $arr
        );

        return json_encode($rtaFinal);
    }

    public function getMisPendientesFilterMasivo(MisPendientesFormRequest $request)
    {
        try {
            $datosRequest = $request->validated()["data"]["attributes"];

            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {

                return $model->whereRaw('
                    uuid in (
                        select id_proceso_disciplinario from (
                        select lpd.id_proceso_disciplinario, max(lpd.id_funcionario_actual) keep (dense_rank first order by lpd.created_at desc) id_funcionario_actual
                        from log_proceso_disciplinario lpd
                        group by lpd.id_proceso_disciplinario
                        ) log
                        where log.ID_FUNCIONARIO_ACTUAL = ? )', $datosRequest["usuario_actual"])
                    ->orderBy('proceso_disciplinario.CREATED_AT', 'desc')->get();
            });
        } catch (\Exception $e) {
            error_log($e);
        }

        return  MisPendientesCollection::make($query);
    }

    public function getMisTransacciones()
    {
        try{
            $query = $this->repository->customQuery(function ($model) {
                return $model->select(
                    'proceso_disciplinario.uuid',
                    'proceso_disciplinario.radicado',
                    'proceso_disciplinario.vigencia',
                    'proceso_disciplinario.id_tipo_proceso',
                    'proceso_disciplinario.estado',
                    'proceso_disciplinario.id_etapa',
                    'proceso_disciplinario.migrado',
                    'proceso_disciplinario.created_at',
                    'log_proceso_disciplinario.created_at AS fecha_actualizacion',
                    'log_proceso_disciplinario.descripcion AS ultima_descripcion_log'
                )->distinct()
                    ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                    ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                    ->where('log_proceso_disciplinario.id_fase', 22)
                    ->where('proceso_disciplinario.estado', 1)
                    ->latest('log_proceso_disciplinario.created_at')
                    ->get();
            });

            $arr = array();
            foreach (MisPendientesCollection::make($query) as $key => $value) {
                //error_log(MisPendientesCollection::make($query)[$key]->getTipoExpediente);

                if (MisPendientesCollection::make($query)[$key]->getTipoExpediente) {

                    if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 1 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_derecho_peticion == 1) {
                        $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 1)->get()->first();
                        $nombre2 = TipoDerechoPeticionModel::query()->select('mas_tipo_derecho_peticion.nombre')->where('id', '=', 1)->get()->first();
                        $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                    } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 1 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_derecho_peticion == 2) {
                        $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 1)->get()->first();
                        $nombre2 = TipoDerechoPeticionModel::query()->select('mas_tipo_derecho_peticion.nombre')->where('id', '=', 2)->get()->first();
                        $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                    } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 1 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_derecho_peticion == 3) {
                        $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 1)->get()->first();
                        $nombre2 = TipoDerechoPeticionModel::query()->select('mas_tipo_derecho_peticion.nombre')->where('id', '=', 3)->get()->first();
                        $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                    } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 2 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_queja == 1) {
                        $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 2)->get()->first();
                        $nombre2 = TipoQuejaModel::query()->select('mas_tipo_queja.nombre')->where('id', '=', 1)->get()->first();
                        $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                    } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 3 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_queja == 1) {
                        $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 3)->get()->first();
                        $nombre2 = TipoQuejaModel::query()->select('mas_tipo_queja.nombre')->where('id', '=', 1)->get()->first();
                        $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                    } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 3 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_queja == 2) {
                        $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 3)->get()->first();
                        $nombre2 = TipoQuejaModel::query()->select('mas_tipo_queja.nombre')->where('id', '=', 2)->get()->first();
                        $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                    } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 4 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_termino_respuesta == 1) {
                        $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 4)->get()->first();
                        $nombre2 = TerminoRespuestaModel::query()->select('mas_termino_respuesta.nombre')->where('id', '=', 1)->get()->first();
                        $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                    } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 4 && MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_termino_respuesta == 2) {
                        $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 4)->get()->first();
                        $nombre2 = TerminoRespuestaModel::query()->select('mas_termino_respuesta.nombre')->where('id', '=', 2)->get()->first();
                        $query2 = array("nombre" => $nombre->nombre . " " . $nombre2->nombre);
                    } else if (MisPendientesCollection::make($query)[$key]->getTipoExpediente->id_tipo_expediente == 5) {
                        $nombre = TipoExpedienteModel::query()->select('mas_tipo_expediente.nombre')->where('id', '=', 5)->get()->first();
                        $query2 = array("nombre" => $nombre->nombre);
                    } else {
                        $query2 = array("nombre" => "Sin Clasificación");
                    }
                } else {
                    $query2 = array("nombre" => "Sin Clasificación");
                }

                //dd(MisPendientesCollection::make($query)[$key]);

                array_push(
                    $arr,
                    array(
                        "type" => "proceso_disciplinario",
                        "attributes" => array(
                            "MisPendientes" => MisPendientesCollection::make($query)[$key],
                            "Clasificacion" => $query2,
                        )
                    )
                );
            }

            $rtaFinal = array(
                "data" => $arr
            );

            return json_encode($rtaFinal);

        } catch (\Exception $e) {
            error_log($e);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MisPendientesFormRequest $request)
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
        dd("Hola");
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
}
