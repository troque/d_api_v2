<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MailTrait;
use App\Http\Controllers\Traits\ProcesoDisciplinarioTrait;
use App\Http\Controllers\Traits\SiriusTrait;
use App\Http\Requests\InformeCierreFormRequest;
use App\Http\Resources\InformeCierre\InformeCierreCollection;
use App\Http\Resources\InformeCierre\InformeCierreResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\DependenciaOrigenModel;
use App\Models\DocumentoSiriusModel;
use App\Models\InformeCierreModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\TbintDocumentoSiriusDescripcionModel;
use App\Models\User;
use App\Repositories\RepositoryGeneric;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class InformeCierreController extends Controller
{
    private $repository;
    use SiriusTrait;
    use MailTrait;
    use ProcesoDisciplinarioTrait;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new InformeCierreModel());
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
    public function store(InformeCierreFormRequest $request)
    {
        try {
            DB::connection()->beginTransaction();

            $datos = $request->validated()["data"]["attributes"];
            $datos['created_user'] = auth()->user()->name;
            $datos['id_dependencia'] = auth()->user()->id_dependencia;

            $datos['estado'] = Constants::ESTADOS['activo'];
            $datos['es_compulsa'] = Constants::ESTADOS['inactivo'];
            $datos['eliminado'] = false;

            $tbintDescripcion = new TbintDocumentoSiriusDescripcionModel();
            $descripcionResultado = $tbintDescripcion->create($datos);

            //INSERTAR DOCUMENTO SIRIUS
            $datos['sirius_track_id'] = $datos['radicado_sirius'];
            $datos['sirius_ecm_id'] = $datos['documento_sirius'];
            $datos['grupo'] = $descripcionResultado->uuid;

            $proceso_disciplinario = $this->obtenerDatosProcesoDisciplinario($datos['id_proceso_disciplinario']);

            $datos['finalizado'] =  $proceso_disciplinario->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion'] ? Constants::ESTADOS['activo'] : Constants::ESTADOS['inactivo'];

            $documentoModel = new DocumentoSiriusModel();
            $resultado = $documentoModel->create($datos); //REGISTRAR UUI DE DESCRIPCION
            $datos['id_documento_sirius'] = $resultado->uuid;
            //INSERTAR DOCUMENTO SIRIUS

            //SE INSERTA LA OPERACION
            $resultado = $this->repository->create($datos);

            //INSERTAR EN LOG
            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $datos['id_proceso_disciplinario'])->update(['id_funcionario_actual' => null]);

            //registramos log
            $logRequest['id_proceso_disciplinario'] = $datos['id_proceso_disciplinario'];
            $logRequest['id_etapa'] =  $datos['id_etapa'];
            $logRequest['id_fase'] = $datos['id_fase']; // antecedentes
            $logRequest['id_tipo_log'] = 2; // Log de tipo Fase
            $logRequest['descripcion'] = 'Se realiza Informe cierre';
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = 3; // Remisionado
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['documentos'] = false;
            $logRequest['id_fase_registro'] = $resultado->uuid;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
            //INSERTAR EN LOG

            //ACTUALIZACION PROCESO
            //ProcesoDiciplinarioModel::where('uuid', $datos['id_proceso_disciplinario'])->update(['estado' => 2]);

            //BUSCAR JEFE SEGUN LINEAMIENTOS DE INFORME CIERRE
            $resultado_dependencia_requerimiento_juzgado = DB::select("
                SELECT
                    rj.id_dependencia_origen,
                    rj.id_dependencia_destino,
                    mdo.nombre AS dependencia,
                    u.name AS jefe,
                    u.email
                FROM
                    requerimiento_juzgado rj
                INNER JOIN mas_dependencia_origen mdo ON rj.id_dependencia_destino = mdo.id
                INNER JOIN users u ON mdo.id_usuario_jefe = u.id
                WHERE rj.id_proceso_disciplinario = '" . $datos['id_proceso_disciplinario'] . "'
                AND mdo.estado = 1
            ");

            /*

            Deje en comentario esta linea porque esto solo funciona en tutela porque tiene habilitada la fase de requerimiento juzgado.

            if(count($resultado_dependencia_requerimiento_juzgado) <= 0){
                error_log("store p1");
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            }
            else if($resultado_dependencia_requerimiento_juzgado[0]->jefe == null){
                $error = new stdClass;
                $error->estado = false;
                $error->error = 'No es posible completar el procedimiento, la dependencia '. $resultado_dependencia_requerimiento_juzgado[0]->dependencia .' no tiene usuario JEFE asignado';
                return $error;
            }*/

            // AGREGO ESTA VALIDACION REEMPLAZANDO LA ANTERIOR TENIENDO ENCUENTA QUE SI EXISTA UN REQUERIMIENTO JUZGADO
            if (count($resultado_dependencia_requerimiento_juzgado) > 0 && $resultado_dependencia_requerimiento_juzgado[0]->jefe == null) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = 'No es posible completar el procedimiento, la dependencia ' . $resultado_dependencia_requerimiento_juzgado[0]->dependencia . ' no tiene usuario JEFE asignado';
                return $error;
            }

            //BUSCAR JEFE DE SECRETARIA COMUN
            /*$resultado_dependencia_secretaria_comun = DB::select("
                SELECT
                    mdo.nombre AS dependencia,
                    u.name AS jefe,
                    u.email
                FROM
                    mas_dependencia_origen mdo
                LEFT OUTER JOIN users u ON mdo.id_usuario_jefe = u.id
                WHERE mdo.id = 310
                AND mdo.estado = 1
            "); //JEFE DE SECRETARIA COMUN
            */

            // SE VALIDA QUE SEA JEFE DE SECRETARIA COMUN POR LO TANTO SE CREA EL PERFIL SECRETARIA COMUN
            $resultado_dependencia_secretaria_comun = DB::select("
                select
                mdo.nombre as dependencia,
                u.name AS jefe,
                u.email
                from mas_dependencia_configuracion mdc
                inner join mas_dependencia_origen mdo on mdc.id_dependencia_origen = mdo.id
                inner join users u on mdo.id_usuario_jefe = u.id
                where mdc.id_dependencia_acceso = 12
                and mdo.estado = 1
            "); //JEFE DE SECRETARIA COMUN


            if (count($resultado_dependencia_secretaria_comun) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = 'La dependencia no tiene asignado el rol secretaria común';
                return $error;
            } else if ($resultado_dependencia_secretaria_comun[0]->jefe == null) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = 'No es posible completar el procedimiento, la dependencia ' . $resultado_dependencia_secretaria_comun[0]->dependencia . ' no tiene usuario JEFE asignado';
                return $error;
            }

            $resultado_radicado = ProcesoDiciplinarioModel::where('uuid', $datos['id_proceso_disciplinario'])->get();

            if (count($resultado_radicado) <= 0) {

                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            }

            if (env('SUBIR_DOCUMENTACION_SIRIUS')) {
                $path = $this->obtenerDocumento($datos['radicado_sirius'], $datos['documento_sirius']);
            }

            if (env('SUBIR_DOCUMENTACION_SIRIUS') == false) {
                $path = storage_path() . '/files' . '/emails/email.pdf';
            }


            if (count($resultado_dependencia_requerimiento_juzgado) > 0 && $resultado_dependencia_requerimiento_juzgado[0]->jefe == null) {
                $this->sendMail(
                    $resultado_dependencia_requerimiento_juzgado[0]->email,
                    $resultado_dependencia_requerimiento_juzgado[0]->jefe,
                    "SINPROC: (" . $resultado_radicado[0]->radicado . ") - VIGENCIA (" . $resultado_radicado[0]->vigencia . ')',
                    $datos['descripcion'],
                    [$path],
                    $resultado_dependencia_secretaria_comun[0]->email,
                    null,
                );
            }


            if (!env('SUBIR_DOCUMENTACION_LOCAL')) { //Valida si debe eliminar archivos dada la configuracion en el ENV
                unlink($path); //Se elimina archivo
            }

            DB::connection()->commit();
            return InformeCierreResource::make($resultado);
        } catch (\Exception $e) {
            error_log($e);
            //dd($e);
            // Woopsy
            DB::connection()->rollBack();
            if ((strpos($e->getMessage(), 'Network') !== false) || (strpos($e->getMessage(), 'Request Entity Too Large') !== false)) {

                $error['estado'] = false;
                $error['error'] = 'El archivo que está adjuntando es mayor de lo que permitido por el sistema.';

                return json_encode($error);
            } else {
                $error['estado'] = false;
                $error['error'] = 'No es posible realizar esta operación, si el problema persiste, comuníquese con el administrador.';

                return json_encode($error);
            }
        }
    }

    /**
     * Obtener la informacion de informe cierre del proceso disciplinario
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_proceso_disciplinario)
    {
        try {

            $respuesta_informe_cierre = $this->repository->customQuery(
                function ($model) use ($id_proceso_disciplinario) {
                    return $model
                        ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
                        ->where('eliminado', false)
                        ->orderBy('created_at')
                        ->get();
                }
            );

            return InformeCierreCollection::make($respuesta_informe_cierre)->tipoExpediente($this->obtenerDatosProcesoDisciplinario($id_proceso_disciplinario));
        } catch (\Exception $e) {
            DB::connection()->rollBack();
            if ((strpos($e->getMessage(), 'Network') !== false) || (strpos($e->getMessage(), 'Request Entity Too Large') !== false)) {

                $error['estado'] = false;
                $error['error'] = 'El archivo que está adjuntando es mayor de lo que permitido por el sistema.';

                return json_encode($error);
            } else {
                $error['estado'] = false;
                $error['error'] = 'No es posible realizar esta operación, si el problema persiste, comuníquese con el administrador.';

                return json_encode($error);
            }
        }
    }

    public function storeArchivar($id_proceso_disciplinario)
    {
        try {
            DB::connection()->beginTransaction();

            $respuesta_informe_cierre = $this->repository->customQuery(
                function ($model) use ($id_proceso_disciplinario) {
                    return $model
                        ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
                        ->where('eliminado', false)
                        ->get();
                }
            );

            if (count($respuesta_informe_cierre) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            }

            //Obtener documento de SIRIUS
            $respuesta_documento = DocumentoSiriusModel::where('uuid', $respuesta_informe_cierre[0]->id_documento_sirius)->latest('created_at')->get();

            if (count($respuesta_documento) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            }

            /*if(env('SUBIR_DOCUMENTACION_SIRIUS')){
                $path = $this->obtenerDocumento($respuesta_documento[0]->sirius_track_id, $respuesta_documento[0]->sirius_ecm_id);
            }*/

            //BUSCAR JEFE DE LA DEPENDENCIA
            $resultado_dependencia = DependenciaOrigenModel::where('id', auth()->user()->id_dependencia)->get();

            if (count($resultado_dependencia) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            } else if ($resultado_dependencia[0]->id_usuario_jefe == null) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = 'No es posible completar el procedimiento, la dependencia ' . $resultado_dependencia[0]->nombre . ' no tiene usuario JEFE asignado';
                return $error;
            }

            $resultado_usuario = User::where('id', $resultado_dependencia[0]->id_usuario_jefe)->get();

            if (count($resultado_usuario) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            }

            $resultado_radicado = ProcesoDiciplinarioModel::where('uuid', $id_proceso_disciplinario)->get();

            if (count($resultado_radicado) <= 0) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            }

            //Obtener archivo

            //ACTUALIZACION PROCESO
            ProcesoDiciplinarioModel::where('uuid', $id_proceso_disciplinario)->update(['estado' => 3]);

            $informe_cierre['id_proceso_disciplinario'] = $id_proceso_disciplinario;
            $informe_cierre['radicado_sirius'] = null;
            $informe_cierre['documento_sirius'] = null;
            $informe_cierre['descripcion'] = 'CIERRE PROCESO';
            $informe_cierre['created_user'] = auth()->user()->name;
            $informe_cierre['id_fase'] = Constants::FASE['informe_cierre'];
            $informe_cierre['id_etapa'] = Constants::ETAPA['evaluacion'];
            $informe_cierre['id_documento_sirius'] = null;
            $informe_cierre['finalizado'] = Constants::ESTADOS['activo'];
            $informe_cierre['id_dependencia'] = auth()->user()->id_dependencia;

            //INFORME CIERRE
            $resultado_informe_cierre = $this->repository->create($informe_cierre);

            //INSERTAR EN LOG
            LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $id_proceso_disciplinario)->update(['id_funcionario_actual' => null]);

            //registramos log
            $logRequest['id_proceso_disciplinario'] = $id_proceso_disciplinario;
            $logRequest['id_etapa'] =  Constants::ETAPA['evaluacion'];
            $logRequest['id_fase'] = Constants::FASE['requerimiento_juzgado']; // antecedentes
            $logRequest['id_tipo_log'] = 2; // Log de tipo Fase
            $logRequest['descripcion'] = 'Se realiza Informe cierre';
            $logRequest['created_user'] = auth()->user()->name;
            $logRequest['id_estado'] = 3; // Remisionado
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia; // Remisionado
            $logRequest['documentos'] = false;
            $logRequest['id_fase_registro'] = $resultado_informe_cierre->uuid;
            //$logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_actual'] = null;
            $logRequest['id_funcionario_asignado'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;

            $logModel = new LogProcesoDisciplinarioModel();
            LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
            //INSERTAR EN LOG

            try {

                /*if(env('SUBIR_DOCUMENTACION_SIRIUS') == false){
                    $path = storage_path() . '/files' . '/emails/email.pdf';
                }*/

                //dd($path);

                // Se captura la informacion del usuario
                $nombreGet = !empty($resultado_usuario[0]->nombre) ? $resultado_usuario[0]->nombre . " " : "";
                $apellidoGet = !empty($resultado_usuario[0]->apellido) ? $resultado_usuario[0]->apellido : "";
                $nombre_usuario = $nombreGet . $apellidoGet;

                $this->sendMail(
                    $resultado_usuario[0]->email,
                    $nombre_usuario,
                    "SINPROC: (" . $resultado_radicado[0]->radicado . ") - VIGENCIA (" . $resultado_radicado[0]->vigencia . ')',
                    'Se realizo ARCHIVO el expediente en la fase de INFORME CIERRE de la etapa de evaluación',
                    NULL,
                    null,
                    null,
                );

                /*if(env('SUBIR_DOCUMENTACION_SIRIUS')){
                    unlink($path);
                }*/
            } catch (\Exception $e) {
                error_log($e);
                //unlink($path);
                DB::connection()->rollBack();
                $error = new stdClass;
                $error->estado = false;
                $error->error = "No fue posible enviar el correo electrónico, si el problema persiste comuníquese con el administrador";
                return $error;
            }

            $respuesta_informe_cierre = InformeCierreCollection::make($respuesta_informe_cierre);

            DB::connection()->commit();

            return $respuesta_informe_cierre;
        } catch (\Exception $e) {
            DB::connection()->rollBack();
            if ((strpos($e->getMessage(), 'Network') !== false) || (strpos($e->getMessage(), 'Request Entity Too Large') !== false)) {

                $error['estado'] = false;
                $error['error'] = 'El archivo que está adjuntando es mayor de lo que permitido por el sistema.';

                return json_encode($error);
            } else {
                $error['estado'] = false;
                $error['error'] = 'No es posible realizar esta operación, si el problema persiste, comuníquese con el administrador.';

                return json_encode($error);
            }
        }
    }

    private function obtenerDocumento($sirius_track_id, $sirius_ecm_id)
    {
        try {
            $documento_descargar = null;
            $documentos_sirius = $this->buscarRadicado($sirius_track_id);

            for ($cont = 0; $cont < count($documentos_sirius['documentoDTOList']); $cont++) {
                if ($documentos_sirius['documentoDTOList'][$cont]['idDocumento'] == $sirius_ecm_id) {
                    $documento_descargar = $documentos_sirius['documentoDTOList'][$cont];
                    $cont = count($documentos_sirius['documentoDTOList']) + 1;
                }
            }

            if ($documento_descargar == null) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error, por favor comuníquese con el Administrador";
                return $error;
            }

            $documento_sirius = $this->buscarDocumento($documento_descargar['idDocumento'], $documento_descargar['versionLabel']);

            //Guardar File
            $path = storage_path() . '/files' . '/emails';

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $path = $path . '/' . $documento_descargar['idDocumento'] . '_' . $documento_descargar['nombreDocumento'];
            $b64 = $documento_sirius['base_64'];
            $bin = base64_decode($b64, true);
            file_put_contents($path, $bin);
            //Guardar File
            return $path;
        } catch (\Exception $e) {
            return null;
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
