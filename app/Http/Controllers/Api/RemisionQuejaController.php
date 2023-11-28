<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ClasificacionTrait;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Controllers\Traits\MailTrait;
use App\Http\Controllers\Traits\MigracionesTrait;
use App\Http\Requests\RemisionQuejaFormRequest;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Resources\ProcesoDiciplinario\ProcesoDiciplinarioResource;
use App\Http\Resources\RemisionQueja\RemisionQuejaCollection;
use App\Http\Resources\RemisionQueja\RemisionQuejaResource;
use App\Http\Utilidades\Constants;
use App\Models\DependenciaOrigenModel;
use App\Models\IncorporacionModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\RemisionQuejaModel;
use App\Models\User;
use App\Repositories\RepositoryGeneric;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class RemisionQuejaController extends Controller
{

    use MailTrait;
    use LogTrait;
    use MigracionesTrait;

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new RemisionQuejaModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RemisionQuejaFormRequest $request)
    {
        try {

            DB::connection()->beginTransaction();

            $datosRecibidos = $request->validated()["data"]["attributes"];
            $proceso_disciplinario = [];
            $resultado_usuario = [];
            $datosRecibidos['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $datosRecibidos['created_user'] = auth()->user()->name;
            $datosRecibidos['eliminado'] = false;

            switch ($datosRecibidos['id_tipo_evaluacion']) {
                case Constants::RESULTADO_EVALUACION['comisorio_eje']:
                    $jefe_delegada = DB::select("
                        SELECT
                            mdo.id as id_dependencia_origen,
                            mdo.nombre,
                            --(SELECT COUNT(*) FROM log_proceso_disciplinario lpd WHERE lpd.id_dependencia_origen = mdc.id_dependencia_origen) AS num_casos,
                            --floor(dbms_random.value(1, 5)) AS num_casos,
                            u.numero_casos AS num_casos,
                            mdo.id_usuario_jefe,
                            mdc.porcentaje_asignacion
                        FROM
                        mas_dependencia_acceso mda
                        INNER JOIN mas_dependencia_configuracion mdc ON mdc.id_dependencia_acceso = mda.id
                        INNER JOIN mas_dependencia_origen mdo ON mdo.id = mdc.id_dependencia_origen
                        INNER JOIN users u ON mdo.id_usuario_jefe = u.id
                        WHERE mda.id = " . $datosRecibidos['id_tipo_dependencia_acceso'] . "
                        AND mdo.estado = 1
                        AND mdo.id_usuario_jefe > 0
                        AND u.estado = 1
                        AND u.reparto_habilitado = 1
                        ORDER BY u.numero_casos ASC
                    ");

                    if (count($jefe_delegada) <= 0) { //No existen jefes asignados a ninguna dependencia
                        $error = new stdClass;
                        $error->estado = false;
                        $error->error = 'No es posible completar el procedimiento, ninguna de las dependencias pertenecientes a COMISORIO EJE tienen almenos un usuario JEFE asignado';
                        return $error;
                    }

                    //Obtener el mayor numero de casos
                    $mayor_numero = $jefe_delegada[0]->num_casos;
                    $depe_porcentajes_menores = [];
                    $cont_2 = 0;
                    $encontro_primera_depen_100 = false;

                    for ($cont = 0; $cont < count($jefe_delegada); $cont++) {
                        if ($mayor_numero < $jefe_delegada[$cont]->num_casos) {
                            $mayor_numero = $jefe_delegada[$cont]->num_casos;
                        }

                        if ($jefe_delegada[$cont]->porcentaje_asignacion < 100) { //Busca la dependencias inferiores a 100%
                            $depe_porcentajes_menores[$cont_2] = $jefe_delegada[$cont];
                            $cont_2++;
                        } else if (!$encontro_primera_depen_100 && $jefe_delegada[$cont]->porcentaje_asignacion == 100) { //Busca la dependencia que tenga 100%
                            $dependencia_reparto = $jefe_delegada[$cont];
                            $encontro_primera_depen_100 = true;
                        }
                    }

                    //Se debe crear un Metodo de ordenamiento para $depe_porcentajes_menores de mayor a menor
                    if (!$encontro_primera_depen_100) { //aplicar dentro del metodo de ordenamiento
                        $dependencia_reparto = $jefe_delegada[0]; //0 temporal
                    }

                    //$dependencias_porcentajes_menores_100 = krsort($dependencias_porcentajes_menores_100);
                    for ($cont = 0; $cont < count($depe_porcentajes_menores); $cont++) {
                        if ($depe_porcentajes_menores[$cont]->num_casos < ($mayor_numero * ($depe_porcentajes_menores[$cont]->porcentaje_asignacion) / 100)) {
                            $dependencia_reparto = $depe_porcentajes_menores;
                            $cont = count($depe_porcentajes_menores) + 1;
                        }
                    }

                    $resultado_usuario = $dependencia_reparto;

                    // Se valida que sea un array
                    if (isset($dependencia_reparto->id_dependencia_origen)) {
                        error_log("ENTRO AQUI");
                        // Se captura la dependencia destino
                        $datosRecibidos['id_dependencia_destino'] = $dependencia_reparto->id_dependencia_origen;
                    } else {

                        // Se captura la dependencia destino
                        $datosRecibidos['id_dependencia_destino'] = $dependencia_reparto->id_dependencia_origen;
                    }
                    break;
                case Constants::RESULTADO_EVALUACION['incorporacion']:
                    $datosRecibidos['vigencia_expediente'] = $datosRecibidos['vigencia'];
                    $datosRecibidos['id_proceso_disciplinario_incorporado'] = $datosRecibidos['id_proceso_disciplinario'];

                    $proceso_disciplinario = DB::select("
                        SELECT
                            pd.uuid,
                            mepd.nombre AS nombre_estado,
                            lpd.created_at
                        FROM
                        proceso_disciplinario pd
                        INNER JOIN mas_estado_proceso_disciplinario mepd ON mepd.id = pd.estado
                        INNER JOIN log_proceso_disciplinario lpd ON lpd.id_proceso_disciplinario = pd.uuid
                        WHERE pd.uuid = '" . $datosRecibidos['id_proceso_disciplinario_expediente'] . "'
                        AND (pd.estado = 2 OR pd.estado = 3)
                        ORDER BY lpd.created_at ASC
                    ");

                    if (count($proceso_disciplinario) > 0) {

                        $proceso_disciplinario_incorporado = DB::select("
                            SELECT
                                pd.uuid,
                                pd.vigencia,
                                pd.radicado AS expediente,
                                mepd.nombre,
                                lpd.id_dependencia_origen,
                                lpd.created_at
                            FROM
                            proceso_disciplinario pd
                            INNER JOIN mas_estado_proceso_disciplinario mepd ON mepd.id = pd.estado
                            INNER JOIN log_proceso_disciplinario lpd ON lpd.id_proceso_disciplinario = pd.uuid
                            WHERE pd.uuid = '" . $datosRecibidos['id_proceso_disciplinario_incorporado'] . "'
                            ORDER BY lpd.created_at ASC
                        ");
                    }

                    //Se alista modelo de JEFE DE LA DEPENDENCIA
                    $repository_dependencia = new RepositoryGeneric();
                    $repository_dependencia->setModel(new DependenciaOrigenModel());

                    //BUSCAR JEFE DE LA DEPENDENCIA ORIGEN
                    //$resultado_dependencia_origen = $repository_dependencia->find($datosRecibidos['id_dependencia_origen']);

                    //BUSCAR JEFE DE LA DEPENDENCIA DESTINO
                    $repository_dependencia_destino = $repository_dependencia->find($datosRecibidos['id_dependencia_destino']);

                    if ($repository_dependencia_destino->id_usuario_jefe == null) {
                        $error = new stdClass;
                        $error->estado = false;
                        $error->error = 'No es posible completar el procedimiento, la dependencia destino no tiene usuario JEFE asignado.';
                        return $error;
                    }

                    $respuesta_jefe = DB::select(
                        "
                        SELECT
                            u.id,
                            u.name,
                            u.estado,
                            u.reparto_habilitado
                        FROM
                            users u
                        WHERE u.id = " . $repository_dependencia_destino->id_usuario_jefe
                    );

                    if (!$respuesta_jefe[0]->estado) {
                        $error = new stdClass;
                        $error->estado = false;
                        $error->error = 'No es posible completar el procedimiento, el usuario JEFE no está activo';
                        return $error;
                    } else if (!$respuesta_jefe[0]->reparto_habilitado) {
                        $error = new stdClass;
                        $error->estado = false;
                        $error->error = 'No es posible completar el procedimiento, el usuario JEFE no está activo para reparto';
                        return $error;
                    }


                    //BUSCAR USUARIO
                    $repository_usuario = new RepositoryGeneric();
                    $repository_usuario->setModel(new User());
                    $resultado_usuario = $repository_usuario->find($repository_dependencia_destino->id_usuario_jefe);

                    if (count($proceso_disciplinario) > 0) {
                        try {
                            $this->sendMail(
                                $resultado_usuario->email,
                                $resultado_usuario->nombre . " " . $resultado_usuario->apellido,
                                "Incorporación al expediente: (" . $datosRecibidos['expediente'] . ") - VIGENCIA (" . $datosRecibidos['vigencia_expediente'] . ')',
                                'Se evaluó una incorporación del expediente: (' . $proceso_disciplinario_incorporado[0]->expediente . ') con vigencia (' . $proceso_disciplinario_incorporado[0]->vigencia . ') al expediente: (' . $datosRecibidos['expediente'] . ') con vigencia (' . $datosRecibidos['vigencia_expediente'] . '), esté último se encuentra ' . $proceso_disciplinario[0]->nombre_estado . ' por su delegada',
                                null,
                                null,
                                null,
                            );
                        } catch (\Exception $e) {
                            error_log($e);
                        }
                    }

                    $respuesta = RemisionQuejaResource::make(IncorporacionModel::create($datosRecibidos));
                    break;
                case '5':
                    //BUSCAR JEFE DE LA DEPENDENCIA
                    $repository_dependencia_origen = new RepositoryGeneric();
                    $repository_dependencia_origen->setModel(new DependenciaOrigenModel());
                    $resultado_dependencia = $repository_dependencia_origen->find($datosRecibidos['id_dependencia_origen']);

                    if ($resultado_dependencia->id_usuario_jefe == null) {
                        $error['estado'] = false;
                        $error['error'] = 'No es posible completar el procedimiento, la dependencia actual no tiene usuario JEFE asignado';

                        return json_encode($error);
                    }

                    //BUSCAR USUARIO
                    $repository_usuario = new RepositoryGeneric();
                    $repository_usuario->setModel(new User());
                    $resultado_usuario = $repository_usuario->find($resultado_dependencia->id_usuario_jefe);

                    break;
                default:
                    break;
            }
            error_log("HOLA");
            error_log($datosRecibidos['id_dependencia_destino']);
            $respuesta = RemisionQuejaResource::make($this->repository->create($datosRecibidos));
            $array = json_decode(json_encode($respuesta));

            // Se registra la dependencia dueña o destino del proceso disciplinario
            ProcesoDiciplinarioModel::where('uuid', $datosRecibidos['id_proceso_disciplinario'])->update(['id_dependencia_duena' => $datosRecibidos['id_dependencia_destino']]);

            //registramos log
            $logRequest['id_proceso_disciplinario'] = $datosRecibidos['id_proceso_disciplinario'];
            $logRequest['id_etapa'] = LogTrait::etapaActual($datosRecibidos['id_proceso_disciplinario']);
            $logRequest['id_fase'] = Constants::FASE['remision_queja'];
            $logRequest['id_tipo_log'] = Constants::TIPO_LOG['fase'];
            $logRequest['descripcion'] = "";
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = 3; // Remisionado
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['documentos'] = false;
            $logRequest['id_fase_registro'] =  $array->id;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_tipo_transaccion'] = Constants::TIPO_DE_TRANSACCION['ninguno'];
            remisionQuejaController::removerFuncionarioActualLog($datosRecibidos['id_proceso_disciplinario']);

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));

            DB::connection()->commit();

            return $respuesta;
        } catch (\Exception $e) {
            error_log($e);
            dd($e);
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
        try {

            $query_remision_queja = $this->repository->customQuery(
                function ($model) use ($id) {
                    return $model->where('id_proceso_disciplinario', $id)->where('eliminado', false)
                        ->get();
                }
            );

            if (count($query_remision_queja) <= 0) {
                return RemisionQuejaCollection::make($query_remision_queja);
            }

            return RemisionQuejaResource::make($query_remision_queja->first());
        } catch (\Exception $e) {
            // Woopsy
            //dd($e);
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    /**
     * Valida en el sistema que el expediente exista de aucuerdo al numero de vigencia, numero de expediente y la dependencia.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function validarExpediente(RemisionQuejaFormRequest $request)
    {
        try {
            $datosRequest = $request->validated()["data"]["attributes"];

            $repository_proceso_disciplinario = new RepositoryGeneric();
            $repository_proceso_disciplinario->setModel(new ProcesoDiciplinarioModel());

            $query_proceso_disciplinario = $repository_proceso_disciplinario->customQuery(function ($model) use ($datosRequest) {
                return $model->where('radicado', $datosRequest['expediente'])
                    ->where('vigencia', $datosRequest['vigencia'])
                    ->where('UUID', '<>', $datosRequest['id_proceso_disciplinario'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            });

            if ($query_proceso_disciplinario->count() == 0) {
                $error['estado'] = false;
                $error['error'] = "No se encontró registrado ningún Proceso Disciplinario asociado a los datos ingresados, es necesario que confirme la información para continuar con el trámite del expediente.";
                $datos['dependencia'] = $datosRequest['id_dependencia_destino'];
                $datos['expediente'] = $datosRequest['expediente'];
                $datos['vigencia'] = $datosRequest['vigencia'];
                $error['variables'] = $datos;
                return json_encode($error);
            } else if ($query_proceso_disciplinario[0]->id_etapa < 3) {
                $error['estado'] = false;
                $error['error'] = "El expediente no se puede incoporar ya que no se trata de un proceso disciplinario.";
                $datos['dependencia'] = $datosRequest['id_dependencia_destino'];
                $datos['expediente'] = $datosRequest['expediente'];
                $datos['vigencia'] = $datosRequest['vigencia'];
                $error['variables'] = $datos;
                return json_encode($error);
            }

            return ProcesoDiciplinarioResource::make($query_proceso_disciplinario[0]);
        } catch (\Exception $e) {
            // Woopsy
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
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
