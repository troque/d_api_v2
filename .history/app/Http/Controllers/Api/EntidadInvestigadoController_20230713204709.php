<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Requests\EntidadInvestigadoFormRequest;
use App\Http\Resources\EntidadInvestigado\EntidadInvestigadoCollection;
use App\Http\Resources\EntidadInvestigado\EntidadInvestigadoResource;
use App\Models\EntidadInvestigadoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use App\Models\EntidadModel;
use App\Http\Resources\Entidad\EntidadCollection;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Resources\Sector\SectorCollection;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\SectorModel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class EntidadInvestigadoController extends Controller
{

    private $repository;
    use LogTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new EntidadInvestigadoModel());
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return EntidadInvestigadoCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EntidadInvestigadoFormRequest $request)
    {
        try {
            $datosRequest = $request->validated()["data"]["attributes"];
            $datosRequest['created_user'] = auth()->user()->name;
            $datosRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $respuesta = EntidadInvestigadoResource::make($this->repository->create($datosRequest));
            $array = json_decode(json_encode($respuesta));

            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $logRequest['id_fase'] = Constants::FASE['entidad_investigado'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
            $logRequest['id_fase_registro'] = $array->id;
            $logRequest['descripcion'] = (isset($datosRequest['observaciones']) && $datosRequest['observaciones']) ? $datosRequest['observaciones'] : "";
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia; // Remisionado
            $logRequest['documentos'] = false;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['ninguno'];
            EntidadInvestigadoController::removerFuncionarioActualLog($datosRequest['id_proceso_disciplinario']);

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            DB::connection()->commit();
            return $respuesta;
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            dd($e);

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

        $query = $this->repository->customQuery(function ($model) use ($id) {
            return $model->where('UUID', $id)->get();
        });

        foreach ($query as $r) {
            if ($r->id_entidad != "") {

                $id_sector = 0;
                $nombreEntidad = $this->getNombreEntidad($r->id_entidad);
                if ($nombreEntidad[0] != null) {
                    $r->nombre_entidad = $nombreEntidad[0]["nombre"];
                    $id_sector = $nombreEntidad[0]["idsector"];
                }

                $nombreSector = $this->getNombreSector($id_sector);
                if ($nombreSector[0] != null) {
                    $r->nombre_sector = $nombreSector[0]["nombre"];
                }
            }
        }

        return EntidadInvestigadoCollection::make($query);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EntidadInvestigadoFormRequest $request, $id)
    {
        try {
            DB::connection()->beginTransaction();
            $datosRequest = $request->validated()["data"]["attributes"];
            $datosRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $datosRequestVal = EntidadInvestigadoResource::make($this->repository->find($id));
            $estadoFinal = (!$datosRequestVal['estado']);
            $estadoInicial = $datosRequestVal['estado'];

            // Se valida que exista por el investigado o planta y contratista
            if (isset($datosRequest["investigado"]) && $datosRequest["investigado"] == false) {
                $mensaje = "no se identifica al presunto investigado." . ' obervaciones: ' . substr($datosRequest['comentario_identifica_investigado'], 0, 3800);;

                // Se actualiza el campo investigado a false y su comentario
                $logRequest['descripcion'] = "Se actualizo el estado del presunto investigado, " . $mensaje;
                $respuesta = EntidadInvestigadoModel::where('UUID', $id)->update(['investigado' => ($datosRequest['investigado']), 'comentario_identifica_investigado' => ($datosRequest['comentario_identifica_investigado']), 'contratista' => null, 'planta' => null]);
            } else if (isset($datosRequest["investigado"]) && $datosRequest["investigado"] == true) {
                $mensaje = "";

                if ($datosRequest["planta"] == true && $datosRequest["contratista"] == false) {
                    $mensaje = "a Planta";
                    $datosRequest['Contratista'] = null;
                } else if ($datosRequest["planta"] == false && $datosRequest["contratista"] == true) {
                    $mensaje = "a Contratista";
                    $datosRequest['planta'] = null;
                } else {
                    $mensaje = "a Planta y Contratista";
                }

                // Se actualiza el campo planta y/o contratista
                $logRequest['descripcion'] = "Se actualizo el estado del presunto investigado " . $mensaje;
                $datosRequest['comentario_identifica_investigado'] = $logRequest["descripcion"];
                $respuesta = EntidadInvestigadoModel::where('UUID', $id)->update(['investigado' => ($datosRequest['investigado']), 'contratista' => ($datosRequest['contratista']), 'planta' => ($datosRequest['planta']), 'comentario_identifica_investigado' => $datosRequest['comentario_identifica_investigado']]);
            } else {
                $respuesta = EntidadInvestigadoModel::where('UUID', $id)->update(['estado' => (!$datosRequestVal['estado'])]);
                $logRequest['descripcion'] = $datosRequest['observaciones'];
            }

            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $logRequest['id_fase'] = Constants::FASE['entidad_investigado'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
            $logRequest['id_fase_registro'] = $id;

            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['documentos'] = false;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_tipo_transaccion'] = ($estadoInicial == '0' ? Constants::TIPO_DE_TRANSACCION['inactivar'] : Constants::TIPO_DE_TRANSACCION['activar']);
            EntidadInvestigadoController::removerFuncionarioActualLog($datosRequest['id_proceso_disciplinario']);

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
     *
     */
    public function getEntidadInvestigadoByIdDisciplinario($procesoDiciplinarioUUID, EntidadInvestigadoFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];

        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID, $datosRequest) {
            return $model->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)
                ->where('estado', $datosRequest['estado'])
                ->orderBy('entidad_investigado.created_at', 'desc')->get();
            //->paginate($datosRequest['per_page'], ['*'], 'antecedentes', $datosRequest['current_page']);
        });


        error_log(count($query));

        if (count($query) > 0) {

            foreach ($query as $r) {

                if ($r->id_entidad != "") {

                    $id_sector = 0;

                    $nombreEntidad = $this->getNombreEntidad($r->id_entidad);
                    if ($nombreEntidad[0] != null) {
                        $r->nombre_entidad = $nombreEntidad[0]["nombre"];
                        $id_sector = $nombreEntidad[0]["idsector"];
                    }

                    $nombreSector = $this->getNombreSector($id_sector);

                    if ($nombreSector[0] != null) {
                        $r->nombre_sector = $nombreSector[0]["nombre"];
                    }
                }
            }
        }



        return EntidadInvestigadoCollection::make($query);
    }



    public function getEntidadInvestigadoFilter($procesoDiciplinarioUUID, EntidadInvestigadoFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        // error_log(json_encode($datosRequest));
        // error_log(json_encode($procesoDiciplinarioUUID));

        $query = EntidadInvestigadoModel::query();
        $query = $query->where('id_proceso_disciplinario', $procesoDiciplinarioUUID);
        $query = $query->leftJoin('proceso_disciplinario', 'id_proceso_disciplinario', '=', 'proceso_disciplinario.uuid');

        if (!empty($datosRequest['nombre_entidad']) && $datosRequest['nombre_entidad'] != "-1") {
            //$query = $query->where('tipo_documento', 'like', '%' . $datosRequest['tipo_documento'] . '%');
            $query = $query->where('nombre_entidad', '=', $datosRequest['nombre_entidad']);
        }
        if (!empty($datosRequest['nombre_investigado']) && $datosRequest['nombre_investigado'] != "-1") {
            $query = $query->where('nombre_investigado', '=', $datosRequest['nombre_investigado']);
        }
        if (!empty($datosRequest['cargo']) && $datosRequest['cargo'] != "-1") {
            $query = $query->where('cargo', '=', $datosRequest['cargo']);
        }


        $query = $query->select(
            'entidad_investigado.uuid',
            'entidad_investigado.id_proceso_disciplinario',
            'entidad_investigado.id_etapa',
            'entidad_investigado.nombre_investigado',
            'entidad_investigado.cargo',
            'entidad_investigado.codigo',
            'entidad_investigado.observaciones',
            'entidad_investigado.estado',
            'entidad_investigado.requiere_registro',
            'entidad_investigado.id_entidad',
            'entidad_investigado.investigado',
            'entidad_investigado.contratista',
            'entidad_investigado.planta',
            'entidad_investigado.comentario_identifica_investigado'
        )->orderBy('entidad_investigado.created_at', 'desc')->get();

        return EntidadInvestigadoCollection::make($query);


        if (empty($query[0])) {
            $error['estado'] = false;
            $error['error'] = 'No se encontro información relacionada, si el error persiste consulte con el Administrador TICS';
            return json_encode($error);
        }

        return EntidadInvestigadoCollection::make($query);
    }



    public function getNombreEntidad($idEntidad)
    {

        $query = EntidadModel::query();
        $query = $query->where("identidad", $idEntidad)->select('entidad.identidad', 'entidad.nombre', 'entidad.direccion', 'entidad.idsector')->orderBy('entidad.nombre', 'asc')->get();

        return EntidadCollection::make($query);
    }


    /**
     *
     */
    public function getNombreSector($idSector)
    {

        $query = SectorModel::query();
        $query = $query->where("idsector", $idSector)->select('sector.idsector', 'sector.nombre', 'sector.idestado')->get();


        return SectorCollection::make($query);
    }
}
