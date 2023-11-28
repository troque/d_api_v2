<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DatosInteresadoFormRequest;
use App\Http\Resources\DatosInteresado\DatosInteresadoCollection;
use App\Http\Resources\DatosInteresado\DatosInteresadoResource;
use App\Models\DatosInteresadoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\EntidadModel;
use App\Http\Resources\Entidad\EntidadCollection;
use App\Http\Controllers\Api\TipoDocumentoController;
use App\Http\Controllers\Api\DepartamentoController;
use App\Http\Controllers\Api\TipoSujetoProcesalController;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ProcesoDiciplinarioModel;
use Illuminate\Support\Facades\DB;
use Error;

class DatosInteresadoController extends Controller
{
    use LogTrait;
    private $repository;

    public function __construct(
        RepositoryGeneric $repository,
        TipoDocumentoController $tipoDocumentoController,
        DepartamentoController $departamentoController,
        TipoSujetoProcesalController $tipoSujetoProcesalController
    ) {
        $this->repository = $repository;
        $this->repository->setModel(new DatosInteresadoModel());
        $this->tipoDocumentoService = $tipoDocumentoController;
        $this->departamentoService = $departamentoController;
        $this->tipoSujetoProcesalServices = $tipoSujetoProcesalController;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->repository->customQuery(function ($model) {
            return $model->leftJoin('interesado', 'id_tipo_proceso', '=', 'mas_tipo_proceso.id')->get();
        });
        return DatosInteresadoCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DatosInteresadoFormRequest $request)
    {
        try {

            DB::connection()->beginTransaction();
            $datosRequest = $request->validated()["data"]["attributes"];

            if ($datosRequest['numero_documento'] != '2030405060') {

                if ($datosRequest['id_tipo_sujeto_procesal'] != null) {
                    $linea = " AND i.id_tipo_sujeto_procesal = " . $datosRequest['id_tipo_sujeto_procesal'];
                } else {
                    $linea = "";
                }

                $query = DB::select("SELECT
                i.numero_documento,
                i.id_tipo_sujeto_procesal,
                (SELECT nombre FROM mas_tipo_sujeto_procesal WHERE id = i.id_tipo_sujeto_procesal) as nombre_sujeto_procesal
                FROM interesado i
                WHERE
                i.id_proceso_disciplinario = '" .  $datosRequest['id_proceso_disciplinario'] . "'
                AND i.numero_documento = '" . $datosRequest['numero_documento'] . "'" . $linea . " AND estado = " . Constants::ESTADOS['activo']);

                if (count($query) > 0) {

                    $error['estado'] = false;
                    $error['error'] = "EL USUARIO IDENTIFICADO CON " . $datosRequest['numero_documento'] . " CON SUJETO PROCESAL " . $query[0]->nombre_sujeto_procesal . " YA ESTÁ REGISTRADO EN EL SISTEMA. INHABILITE EL REGISTRO QUE YA ESTÁ REGISTRADO Y REGISTRELO NUEVAMENTE EN CASO DE SER NECESARIO.";
                    return json_encode($error);
                }
            }


            $datosRequest['fecha_registro'] = date('Y-m-d H:m:s');
            $datosRequest['estado'] = 1;
            $datosRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $datosRequest['id_funcionario'] = 1;
            $datosRequest['created_user'] = auth()->user()->name;
            $datosRequest['id_dependencia'] = auth()->user()->id_dependencia;

            $respuesta = DatosInteresadoResource::make($this->repository->create($datosRequest));

            $array = json_decode(json_encode($respuesta));

            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $logRequest['id_fase'] = Constants::FASE['datos_interesado'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
            $logRequest['id_fase_registro'] = $array->id;
            $logRequest['descripcion'] = "Registro inicial";
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['documentos'] = false;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['ninguno'];
            DatosInteresadoController::removerFuncionarioActualLog($datosRequest['id_proceso_disciplinario']);

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return DatosInteresadoResource::make($this->repository->find($id));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DatosInteresadoFormRequest $request, $id)
    {
        try {
            DB::connection()->beginTransaction();
            $datosRequest = $request->validated()["data"]["attributes"];

            $datosRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $datosRequestVal = DatosInteresadoResource::make($this->repository->find($id));
            $estadoFinal = (!$datosRequestVal['estado']);
            $estadoInicial = $datosRequestVal['estado'];

            if ($estadoInicial ==  Constants::ESTADOS['inactivo'] && $datosRequestVal['numero_documento'] != '2030405060') {

                if ($datosRequest['id_tipo_sujeto_procesal'] != null) {
                    $linea = " AND i.id_tipo_sujeto_procesal = " . $datosRequest['id_tipo_sujeto_procesal'];
                } else {
                    $linea = "";
                }

                $query = DB::select("SELECT
                i.numero_documento,
                i.id_tipo_interesao,
                (SELECT nombre FROM mas_tipo_sujeto_procesal WHERE id = i.id_tipo_sujeto_procesal) as nombre_sujeto_procesal
                FROM interesado i
                WHERE
                i.id_proceso_disciplinario = '" .  $datosRequest['id_proceso_disciplinario'] . "'
                AND i.numero_documento = '" . $datosRequestVal['numero_documento'] . $linea . " AND estado = " . $estadoFinal);

                if (count($query) > 0) {

                    $error['estado'] = false;
                    $error['error'] = "EL USUARIO IDENTIFICADO CON " . $query[0]->numero_documento . " CON SUJETO PROCESAL " . $query[0]->nombre_sujeto_procesal . " YA ESTÁ REGISTRADO COMO ACTIVO EN EL SISTEMA. INHABILITE EL REGISTRO QUE YA ESTÁ REGISTRADO PARA HABILITAR ESTE INTERESADO.";
                    return json_encode($error);
                }
            }

            $respuesta = DatosInteresadoModel::where('UUID', $id)->update(['estado' => (!$datosRequestVal['estado'])]);

            $logRequest['id_proceso_disciplinario'] = $datosRequest['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = LogTrait::etapaActual($datosRequest['id_proceso_disciplinario']);
            $logRequest['id_fase'] = Constants::FASE['datos_interesado'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
            $logRequest['id_fase_registro'] = $id;
            $logRequest['descripcion'] = $datosRequest['estado_observacion'];
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido'];
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['documentos'] = false;
            $logRequest['id_fase_registro'] = $id;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_tipo_transaccion'] = ($estadoInicial == '0' ? Constants::TIPO_DE_TRANSACCION['inactivar'] : Constants::TIPO_DE_TRANSACCION['activar']);
            DatosInteresadoController::removerFuncionarioActualLog($datosRequest['id_proceso_disciplinario']);

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
    public function getDatosInteresadoByIdDisciplinario($procesoDiciplinarioUUID, DatosInteresadoFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];

        $query = $this->repository->customQuery(function ($model) use ($procesoDiciplinarioUUID, $datosRequest) {
            //return $model->get();
            return $model
                ->where('id_proceso_disciplinario', $procesoDiciplinarioUUID)
                ->where('estado', $datosRequest['estado'])
                ->orderBy('interesado.CREATED_AT', 'desc')->get();
            //->paginate($datosRequest['per_page'], ['*'], 'interesado', $datosRequest['current_page']);
        });


        foreach ($query as $r) {
            if ($r->id_entidad != "") {

                $nombreEntidad = $this->getNombreEntidad($r->id_entidad);
                if ($nombreEntidad[0] != null) {
                    $r->nombre_entidad = $nombreEntidad[0]["nombre"];
                }
            }
        }


        return DatosInteresadoCollection::make($query);
    }

    public function getDatosInteresadoById($id)
    {
        // error_log($id);
        $query = $this->repository->customQuery(function ($model) use ($id) {
            return $model->where('UUID', $id)

                ->leftJoin('mas_tipo_documento', 'interesado.TIPO_DOCUMENTO', '=', 'mas_tipo_documento.id')
                ->leftJoin('mas_tipo_sujeto_procesal', 'interesado.ID_TIPO_SUJETO_PROCESAL', '=', 'mas_tipo_sujeto_procesal.id')
                ->leftJoin('MAS_TIPO_INTERESADO', 'interesado.ID_TIPO_INTERESAO', '=', 'MAS_TIPO_INTERESADO.id')
                ->leftJoin('MAS_TIPO_SUJETO_PROCESAL', 'interesado.ID_TIPO_SUJETO_PROCESAL', '=', 'MAS_TIPO_SUJETO_PROCESAL.id')
                ->leftJoin('mas_localidad', 'interesado.id_localidad', '=', 'mas_localidad.id')
                ->leftJoin('mas_sexo', 'interesado.id_sexo', '=', 'mas_sexo.id')
                ->leftJoin('mas_genero', 'interesado.id_genero', '=', 'mas_genero.id')
                ->leftJoin('mas_orientacion_sexual', 'interesado.ID_ORIENTACION_SEXUAL', '=', 'mas_orientacion_sexual.id')
                ->leftJoin('mas_tipo_entidad', 'interesado.ID_TIPO_ENTIDAD', '=', 'mas_tipo_entidad.id')
                ->select(
                    'interesado.uuid',
                    'interesado.id_tipo_interesao',
                    'interesado.id_tipo_sujeto_procesal',
                    'interesado.id_proceso_disciplinario',
                    'interesado.tipo_documento',
                    'interesado.numero_documento',
                    'interesado.primer_nombre',
                    'interesado.segundo_nombre',
                    'interesado.primer_apellido',
                    'interesado.segundo_apellido',
                    'interesado.id_departamento',
                    'interesado.id_ciudad',
                    'interesado.direccion',
                    'interesado.id_localidad',
                    'interesado.email',
                    'interesado.telefono_celular',
                    'interesado.telefono_fijo',
                    'interesado.id_sexo',
                    'interesado.id_genero',
                    'interesado.id_orientacion_sexual',
                    'interesado.entidad',
                    'interesado.cargo',
                    'interesado.tarjeta_profesional',
                    'interesado.id_dependencia',
                    'interesado.id_tipo_entidad',
                    'interesado.nombre_entidad',
                    'interesado.id_entidad',
                    'interesado.id_funcionario',
                    'interesado.estado',
                    'interesado.folio',
                    'interesado.id_dependencia_entidad',
                    'interesado.autorizar_envio_correo',
                    'interesado.direccion_json',
                    'interesado.created_user',
                    'interesado.created_at',
                    'mas_tipo_documento.nombre AS nombre_tipo_documento',
                    'mas_tipo_sujeto_procesal.nombre as nombre_sujeto_procesal',
                    'MAS_TIPO_INTERESADO.nombre as nombre_tipo_interesado',
                    'MAS_TIPO_SUJETO_PROCESAL.nombre as sujeto_procesal_nombre',
                    'mas_localidad.nombre as nombre_localidad',
                    'mas_sexo.nombre as nombre_sexo',
                    'mas_genero.nombre as nombre_genero',
                    'mas_orientacion_sexual.nombre as nombre_orientacion',
                    'mas_tipo_entidad.nombre as nombre_tipo_entidad'
                )->get();
        });

        foreach ($query as $r) {
            if ($r->id_entidad != "") {

                $nombreEntidad = $this->getNombreEntidad($r->id_entidad);
                if ($nombreEntidad[0] != null) {
                    $r->nombre_entidad = $nombreEntidad[0]["nombre"];
                }
            }
        }


        return DatosInteresadoCollection::make($query);
    }

    public function getInteresadoAntecedenteTipoNumero(DatosInteresadoFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        $retorno = [];
        $procesoDiciplinarioUUID = $datosRequest["id_proceso_disciplinario"];
        $results = DB::select(DB::raw("select p.vigencia, a.created_at, p.radicado, (select tp.nombre from mas_tipo_proceso tp where tp.id = p.id_tipo_proceso) as nombre_tipo_proceso,
        a.descripcion, a.created_user, (select d.nombre from mas_dependencia_origen d where d.id = a.id_dependencia) as nombre_dependencia,
        (select a2.descripcion from antecedente a2 where a2.id_proceso_disciplinario = a.id_proceso_disciplinario
        and a2.created_at = (select min(a3.created_at) from antecedente a3 where a3.id_proceso_disciplinario = a2.id_proceso_disciplinario)) as descripcion2,

        (select a2.created_at from antecedente a2 where a2.id_proceso_disciplinario = a.id_proceso_disciplinario
        and a2.created_at = (select min(a3.created_at) from antecedente a3 where a3.id_proceso_disciplinario = a2.id_proceso_disciplinario)) as created_at2,

        (select (select userr.nombre ||' '||userr.apellido from users userr where userr.name = a2.created_user and rownum =1) from antecedente a2 where a2.id_proceso_disciplinario = a.id_proceso_disciplinario
        and a2.created_at = (select min(a3.created_at) from antecedente a3 where a3.id_proceso_disciplinario = a2.id_proceso_disciplinario)) as created_user2,

        (select (select d.nombre from mas_dependencia_origen d where d.id = a2.id_dependencia) as nombre_dependencia from antecedente a2 where a2.id_proceso_disciplinario = a.id_proceso_disciplinario
        and a2.created_at = (select min(a3.created_at) from antecedente a3 where a3.id_proceso_disciplinario = a2.id_proceso_disciplinario)) as nombre_dependencia2

        from antecedente a
        inner join interesado i on i.id_proceso_disciplinario = a.id_proceso_disciplinario
        inner join proceso_disciplinario p on p.uuid = a.id_proceso_disciplinario
        where i.tipo_documento = :tipoDocumento and i.numero_documento = :numeroDocumento
        and  a.created_at = (select max(a3.created_at) from antecedente a3 where a3.id_proceso_disciplinario = a.id_proceso_disciplinario)
        and a.estado = 1 and a.id_proceso_disciplinario = a.id_proceso_disciplinario GROUP BY p.radicado,p.vigencia, a.created_at, p.radicado ,a.descripcion,
        a.created_user, a.id_dependencia, p.id_tipo_proceso,a.id_proceso_disciplinario"), array(
            'tipoDocumento' => $datosRequest["tipo_documento"],
            'numeroDocumento' => $datosRequest["numero_documento"],
        ));





        return  json_encode($results);
    }


    public function getNombreEntidad($idEntidad)
    {


        $query = EntidadModel::query();
        $query = $query->where("identidad", $idEntidad)->select('entidad.nombre')->orderBy('entidad.nombre', 'asc')->get();

        return EntidadCollection::make($query);
    }

    /**
     * Metodo encargado de buscar los datos del interesado con el numero del radicado
     */
    public function getDatosInteresadoByRadicado($radicado)
    {
        // Se inicializa los modelos de query
        $datosInteresadoModel = DatosInteresadoModel::query();
        $procesoDisciplinarioModel = ProcesoDiciplinarioModel::query();

        // Se realiza la consulta del proceso disciplinario
        $queryProcesoDisciplinario = $procesoDisciplinarioModel->select('proceso_disciplinario.uuid')
            ->where('radicado', $radicado)
            ->get();

        // Se valida que haya informacion de los datos del interesado
        if (empty($queryProcesoDisciplinario)) {

            // Se retorna el mensaje de error
            return [
                "error" => true,
                "mensaje" => "No existe proceso disciplinario con este número del radicado."
            ];
        }

        // Se captura el uuid del proceso disciplinario
        $uuidProcesoDisciplinario = isset($queryProcesoDisciplinario[0]->uuid) ? $queryProcesoDisciplinario[0]->uuid : "";

        // Se valida que haya un proceso disciplinario
        if (empty($uuidProcesoDisciplinario)) {

            // Se retorna el mensaje de error
            return [
                "error" => true,
                "mensaje" => "No se ha encontrado proceso disciplinario con este número de proceso."
            ];
        }

        // Se realiza la consulta
        $queryDatosInteresado = $datosInteresadoModel->select(
            'interesado.uuid',
            'interesado.id_etapa',
            'interesado.id_tipo_interesao',
            'interesado.id_tipo_sujeto_procesal',
            'interesado.id_proceso_disciplinario',
            'interesado.tipo_documento',
            'interesado.numero_documento',
            'interesado.primer_nombre',
            'interesado.segundo_nombre',
            'interesado.id_departamento',
            'interesado.id_ciudad',
            'interesado.direccion',
            'interesado.id_localidad',
            'interesado.email',
            'interesado.telefono_celular',
            'interesado.telefono_fijo',
            'interesado.id_sexo',
            'interesado.id_genero',
            'interesado.id_orientacion_sexual',
            'interesado.entidad',
            'interesado.cargo',
            'interesado.tarjeta_profesional',
            'interesado.id_dependencia',
            'interesado.id_tipo_entidad',
            'interesado.nombre_entidad',
            'interesado.id_entidad',
            'interesado.id_funcionario',
            'interesado.estado',
            'interesado.folio',
            'interesado.id_dependencia_entidad',
            'interesado.autorizar_envio_correo',
            'interesado.direccion_json',
        )
            ->where("id_proceso_disciplinario", $uuidProcesoDisciplinario)
            ->whereIn("tipo_documento",  [1, 2, 3])
            ->orderBy('created_at', 'asc')
            ->get();

        // Se valida que haya informacion de los datos del interesado
        if (empty($queryDatosInteresado)) {

            // Se retorna el mensaje de error
            return [
                "error" => true,
                "mensaje" => "No existen datos del interesado con este número del radicado."
            ];
        }
        // Se valida que haya informacion de los datos del interesado
        if (count($queryDatosInteresado) == 0) {

            // Se retorna el mensaje de error
            return [
                "error" => true,
                "mensaje" => "No existen datos del interesado con este número del radicado."
            ];
        }

        // Se retorna la informacion con el modelo
        return DatosInteresadoCollection::make($queryDatosInteresado);
    }
}
