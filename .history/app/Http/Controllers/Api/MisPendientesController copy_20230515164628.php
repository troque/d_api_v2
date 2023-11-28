<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MisPendientesFormRequest;
use App\Http\Resources\MisPendientes\MisPendientesCollection;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\TipoQuejaModel;
use App\Models\TipoDerechoPeticionModel;
use App\Models\TerminoRespuestaModel;
use App\Models\TipoExpedienteModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

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


    public function getMisPendientesFilter(MisPendientesFormRequest $request)
    {

        $datosRequest = $request->validated()["data"]["attributes"];

        if (!empty($datosRequest['fecha'])) {

            if ($datosRequest['fecha'] == date('Y-m-d')) {

                $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                    return $model->select(
                            'proceso_disciplinario.uuid',
                            'proceso_disciplinario.radicado',
                            'proceso_disciplinario.vigencia',
                            'proceso_disciplinario.id_tipo_proceso',
                            'proceso_disciplinario.estado',
                            'proceso_disciplinario.id_etapa',
                            'log_proceso_disciplinario.created_at'
                        )->orderBy('log_proceso_disciplinario.created_at', 'desc')
                        ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                        ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                        ->whereDate('log_proceso_disciplinario.created_at', '=',  $datosRequest['fecha'])
                        ->where('proceso_disciplinario.estado', 1)
                        ->get();
                });
            } else {
                $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                    return $model->select(
                            'proceso_disciplinario.uuid',
                            'proceso_disciplinario.radicado',
                            'proceso_disciplinario.vigencia',
                            'proceso_disciplinario.id_tipo_proceso',
                            'proceso_disciplinario.estado',
                            'proceso_disciplinario.id_etapa',
                            'log_proceso_disciplinario.created_at'
                        )->orderBy('log_proceso_disciplinario.created_at', 'desc')
                        ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                        ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                        ->whereDate('log_proceso_disciplinario.created_at', '>=',  $datosRequest['fecha'])
                        ->where('proceso_disciplinario.estado', 1)
                        ->get();
                });
            }
        } else {
            $query = $this->repository->customQuery(function ($model) {
                return $model->select(
                        'proceso_disciplinario.uuid',
                        'proceso_disciplinario.radicado',
                        'proceso_disciplinario.vigencia',
                        'proceso_disciplinario.id_tipo_proceso',
                        'proceso_disciplinario.estado',
                        'proceso_disciplinario.id_etapa',
                        'log_proceso_disciplinario.created_at'
                    )->distinct()
                    ->leftJoin('log_proceso_disciplinario', 'log_proceso_disciplinario.id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid')
                    ->where('log_proceso_disciplinario.id_funcionario_actual', auth()->user()->name)
                    ->where('proceso_disciplinario.estado', 1)
                    ->latest('log_proceso_disciplinario.created_at')
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
