<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Requests\AntecedenteFormRequest;
use App\Http\Resources\Antecedente\AntecedenteCollection;
use App\Http\Resources\Antecedente\AntecedenteResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\AntecedenteModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;


/**
 * Clase que gestiona las operaciones relacionadas con los antecdentes de un proceso disciplinario
 * @autor: Sandra Saavedra
 * @Fecha: 27 diciembre 2021
 */
class AntecedenteController extends Controller
{

    private $repository;
    use LogTrait;


    /**
     * Método contructor
     *
     * @param RepositoryGeneric $repository
     */
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new AntecedenteModel());
    }

    /**
     * Trae la lista de antendecentes resgistrados en el sistema.
     *
     * @return AntecedenteCollection
     */
    public function index(Request $request)
    {
        return AntecedenteCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Registra un nuevo antecedente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return AntecedenteResource
     */
    public function store(AntecedenteFormRequest $request)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRequest = $request->validated()["data"]["attributes"];
            $datosRequest['fecha_registro'] = date('Y-m-d H:m:s');
            $datosRequest['estado'] = 1;
            $datosRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $datosRequest['descripcion'] = substr($datosRequest['descripcion'], 0, 4000);
            $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;

            $respuesta = AntecedenteResource::make($this->repository->create($datosRequest));
            $array = json_decode(json_encode($respuesta));

            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $logRequest['id_fase'] = Constants::FASE['antecedentes'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
            $logRequest['descripcion'] = $datosRequest['descripcion'];
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['documentos'] = false;
            $logRequest['id_fase_registro'] = $array->id;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['ninguno'];
            AntecedenteController::removerFuncionarioActualLog($datosRequest['id_proceso_disciplinario']);

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
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
     * Muestra un antecedente específico.
     *
     * @param  int  $id
     * @return AntecedenteResource
     */
    public function show($id)
    {
        return AntecedenteResource::make($this->repository->find($id));
    }


    /**
     *
     * Trae todos los antecdentes que tiene un proceso disciplinario filtrado por el estado. 1: Activo 2: Inactivo
     *
     * @param mixed $procesoDiciplinarioUUID
     * @param AntecedenteFormRequest $request
     *
     * @return AntecedenteCollection
     */
    public function getAllAntecedentesByIdProcesoDisciplinario($procesoDiciplinarioUUID, AntecedenteFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];

        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID, $datosRequest) {
            return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)
                ->where('estado', $datosRequest['estado'])
                ->orderBy('created_at', 'desc')->get();
            //->paginate($datosRequest['per_page'], ['*'], 'antecedentes', $datosRequest['current_page']);
        });

        return AntecedenteCollection::make($query);
    }


    /**
     * Método que trae el primer y último antecedente. Esto se visualiza en las ramas del proceso
     *
     * @param mixed $procesoDiciplinarioUUID
     * @param AntecedenteFormRequest $request
     *
     * @return Json
     */
    public function getPrimerYUltimoAntecedente($procesoDiciplinarioUUID, AntecedenteFormRequest $request)
    {

        $someVariable = $procesoDiciplinarioUUID;

        $results = DB::select(DB::raw("select a.uuid, a.descripcion, a.fecha_registro, (select m.nombre from MAS_DEPENDENCIA_ORIGEN m where m.id = a.ID_DEPENDENCIA)as nombre_dependencia,
        (select m.id from MAS_DEPENDENCIA_ORIGEN m where m.id = a.ID_DEPENDENCIA)as id_dependencia
        from antecedente a
        where (a.created_at = (select min(a2.created_at) from antecedente a2 where a2.id_proceso_disciplinario = :somevariable)
        or a.created_at = (select max(a3.created_at) from antecedente a3 where a3.id_proceso_disciplinario = :somevariable))
        and estado = 1 and a.id_proceso_disciplinario = :somevariable"), array(
            'somevariable' => $someVariable,
        ));


        return  json_encode($results);
    }

    /**
     * Se actualiza el estado de un antendente: 1: Activo 2: Inactivo
     *
     * @param AntecedenteFormRequest $request
     * @param mixed $id
     *
     * @return AntecedenteModel
     */
    public function update(AntecedenteFormRequest $request, $id)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRequest = $request->validated()["data"]["attributes"];
            $datosRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);

            $datosRequestVal = AntecedenteResource::make($this->repository->find($id));
            $estadoFinal = (!$datosRequestVal['estado']);
            $estadoInicial = $datosRequestVal['estado'];

            $respuesta = AntecedenteModel::where('UUID', $id)->update(['estado' => (!$datosRequestVal['estado'])]);

            //registramos log
            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $logRequest['id_fase'] = Constants::FASE['antecedentes'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
            $logRequest['descripcion'] = 'CAMBIO DE ESTADO ' . ($estadoInicial == '0' ? "INACTIVO" : "ACTIVO") . ' A ' . ($estadoFinal == '0' ? "INACTIVO" : "ACTIVO") . ', OBSEVACIONES: ' . substr($datosRequest['estado_observacion'], 0, 3800);
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia; // Remisionado
            $logRequest['documentos'] = false;
            $logRequest['id_fase_registro'] = $id;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['ninguno'];
            AntecedenteController::removerFuncionarioActualLog($datosRequest['id_proceso_disciplinario']);

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            DB::connection()->commit();
            return $respuesta;
        } catch (QueryException  $e) {

            $error['estado'] = false;
            $error['error'] = $e->getMessage();
            return json_encode($error);
        }
    }
}
