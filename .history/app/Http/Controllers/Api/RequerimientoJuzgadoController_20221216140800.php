<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Requests\RequerimientoJuzgadoFormRequest;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Resources\RequerimientoJuzgado\RequerimientoJuzgadoCollection;
use App\Http\Resources\RequerimientoJuzgado\RequerimientoJuzgadoResource;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\RequerimientoJuzgadoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequerimientoJuzgadoController extends Controller
{

    private $repository;
    use LogTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new RequerimientoJuzgadoModel());
    }

    /**
     * Trae la lista de antendecentes resgistrados en el sistema.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return RequerimientoJuzgadoCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Registra un nuevo antecedente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequerimientoJuzgadoFormRequest $request)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRequest = $request->validated()["data"]["attributes"];

            // SE ASIGNA AL JEFE DE LA DELEGADA
            $clasificacion_radicado = DB::select(
                "
                select uuid from clasificacion_radicado where id_proceso_disciplinario = '" . $datosRequest['id_proceso_disciplinario'] . "' and estado = 1"
            );

            $datosRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $datosRequest['id_clasificacion_radicado'] = $clasificacion_radicado[0]->uuid;
            $datosRequest['created_user'] = auth()->user()->name;
            $datosRequest['created_user'] = auth()->user()->name;
            $datosRequest['eliminado'] = false;

            if ($datosRequest['id_dependencia_origen'] == null) {
                $datosRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            }

            if ($datosRequest['id_dependencia_destino'] == null) {
                $datosRequest['id_dependencia_destino'] = auth()->user()->id_dependencia;
            }

            if ($datosRequest['id_funcionario_asignado'] == null) {
                $datosRequest['id_funcionario_asignado'] = auth()->user()->name;
            }

            $datosRequest['created_user'] = auth()->user()->name;

            $respuesta = RequerimientoJuzgadoResource::make($this->repository->create($datosRequest));
            $array = json_decode(json_encode($respuesta));

            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $logRequest['id_fase'] = Constants::FASE['requerimiento_juzgado'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
            $logRequest['descripcion'] = $datosRequest['descripcion'];
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['documentos'] = false;
            $logRequest['id_fase_registro'] = $array->id;
            $logRequest['id_funcionario_actual'] = $datosRequest['id_funcionario_asignado'];
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = $datosRequest['id_funcionario_asignado'];
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['reasignacion'];
            RequerimientoJuzgadoController::removerFuncionarioActualLog($datosRequest['id_proceso_disciplinario']);

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            DB::connection()->commit();

            return $respuesta;
        } catch (\Exception $e) {
            error_log($e);
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
        return RequerimientoJuzgadoResource::make($this->repository->find($id));
    }


    /**
     *
     */
    public function getRequerimientoJuzgadoByIdProcesoDisciplinario($id_proceso_disciplibnario)
    {
        $query = $this->repository->customQuery(function ($model) use ($id_proceso_disciplibnario) {
            return $model->where('id_proceso_disciplinario', $id_proceso_disciplibnario)
                ->where('eliminado', false)
                ->orderBy('created_at', 'desc')->get();
        });

        return RequerimientoJuzgadoCollection::make($query);
    }
}
