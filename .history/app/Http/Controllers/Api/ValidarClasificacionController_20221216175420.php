<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Requests\ValidarClasificacionFormRequest;
use App\Http\Resources\ClasificacionRadicado\ClasificacionRadicadoResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Resources\ValidarClasificacion\ValidarClasificacionCollection;
use App\Http\Resources\ValidarClasificacion\ValidarClasificacionResource;
use App\Http\Utilidades\Constants;
use App\Models\ClasificacionRadicadoModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ValidarClasificacionModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidarClasificacionController extends Controller
{
    private $repository;
    use LogTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ValidarClasificacionModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ValidarClasificacionCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidarClasificacionFormRequest $request)
    {

        $datosRequest = $request->validated()["data"]["attributes"];
        $datosRequest['eliminado'] = false;

        $clasificacion_radicado = new RepositoryGeneric();
        $clasificacion_radicado->setModel(new ClasificacionRadicadoModel());
        $query = $clasificacion_radicado->customQuery(function ($model) use ($datosRequest) {
            return
                $model->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])
                ->where('estado', true)
                ->get();
        });

        $clasificado['id_proceso_disciplinario'] = $query[0]->id_proceso_disciplinario;
        $clasificado['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
        $clasificado['id_tipo_expediente'] = $query[0]->id_tipo_expediente;
        $clasificado['observaciones'] = $query[0]->observaciones;
        $clasificado['id_tipo_queja'] = $query[0]->id_tipo_queja;
        $clasificado['id_termino_respuesta'] = $query[0]->id_termino_respuesta;
        $clasificado['fecha_termino'] = $query[0]->fecha_termino;
        $clasificado['hora_termino'] = $query[0]->hora_termino;
        $clasificado['gestion_juridica'] = $query[0]->gestion_juridica;
        $clasificado['estado'] = $query[0]->estado;
        $clasificado['id_estado_reparto'] = $query[0]->id_estado_reparto;
        $clasificado['oficina_control_interno'] = $query[0]->oficina_control_interno;
        $clasificado['id_tipo_derecho_peticion'] = $query[0]->id_tipo_derecho_peticion;
        $clasificado['created_user'] = auth()->user()->name;
        $clasificado['per_page'] = $query[0]->per_page;
        $clasificado['current_page'] = $query[0]->current_page;
        $clasificado['reclasificacion'] = $query[0]->reclasificacion;
        $clasificado['reparto'] = $query[0]->reparto;
        $clasificado['id_dependencia'] = $query[0]->id_dependencia;
        $clasificado['validacion_jefe'] = $query[0]->validacion_jefe;
        $clasificado['id_fase'] = $query[0]->id_fase;

        // ACTUALIZA TODOS EL HISTORIAL EN INACTIVO PARA DEJAR SOLAMENTE EL ÚLTIMO COMO ACTIVO.
        ClasificacionRadicadoModel::where('estado', 1)->where('id_proceso_disciplinario', $datosRequest['id_proceso_disciplinario'])->update(['estado' => 0]);

        $clasificacionModel = new ClasificacionRadicadoModel();
        ClasificacionRadicadoResource::make($clasificacionModel->create($clasificado));

        // error_log("NUEVO CLASIFICADO: ");
        $datosRequest['id_clasificacion_radicado'] = $query[0]->uuid;
        $datosRequest['created_user'] = auth()->user()->name;

        $respuesta = ValidarClasificacionResource::make($this->repository->create($datosRequest));
        $array = json_decode(json_encode($respuesta));

        //registramos log
        $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
        $logRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
        $logRequest['id_fase'] = Constants::FASE['validacion_clasificacion'];
        $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
        $logRequest['descripcion'] = 'Se valida la clasificacion del radicado';
        $logRequest['created_user'] = auth()->user()->name;
        $logRequest['id_estado'] = 3; // Remisionado
        $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia; // Remisionado
        $logRequest['documentos'] = false;
        $logRequest['id_fase_registro'] = $array->id;
        $logRequest['id_funcionario_actual'] = auth()->user()->name;
        $logRequest['id_funcionario_registra'] = auth()->user()->name;
        $logRequest['id_funcionario_asignado'] = auth()->user()->name;
        $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['ninguno'];
        ValidarClasificacionController::removerFuncionarioActualLog($datosRequest['id_proceso_disciplinario']);

        $logModel = new LogProcesoDisciplinarioModel();
        LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

        DB::connection()->commit();
        return $respuesta;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ValidarClasificacionResource::make($this->repository->find($id));
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

    public function getValidarClasificado($id_clasificado)
    {

        $query = $this->repository->customQuery(function ($model) use ($id_clasificado) {
            return $model->where('id_proceso_disciplinario', $id_clasificado)->get();
        });

        if (!empty($query[0])) {
            return ValidarClasificacionCollection::make($query);
        } else {
            $error['estado'] = false;
            $error['error'] = "No se ha validado el clasificado";
            return json_encode($error);
        }
    }


    /**
     *
     */
    public function getValidarClasificadoPorJefe($id_proceso_disciplinario)
    {
        $respuesta = DB::select("
            select id_proceso_disciplinario
            from clasificacion_radicado
            where id_proceso_disciplinario = " . $id_proceso_disciplinario . "
            and estado = " . Constants::ESTADOS['activo'] . " and validacion_jefe = " . Constants::ESTADOS['activo']);

        if ($respuesta != null) {
            $datos['validacion_jefe'] = true;
            $json['data']['attributes'] = $datos;
        } else {
            $datos['validacion_jefe'] = false;
            $json['data']['attributes'] = $datos;
        }

        return $json;
    }

    public function getTituloProceso($id_proceso_disciplinario)
    {
        $respuesta = DB::select("
            select id_proceso_disciplinario
            from clasificacion_radicado
            where id_proceso_disciplinario = " . $id_proceso_disciplinario . "
            and estado = " . Constants::ESTADOS['activo'] . " and validacion_jefe = " . Constants::ESTADOS['activo']);

        if ($respuesta != null) {
            $datos['validacion_jefe'] = true;
            $json['data']['attributes'] = $datos;
        } else {
            $datos['validacion_jefe'] = false;
            $json['data']['attributes'] = $datos;
        }

        return $json;
    }
}
