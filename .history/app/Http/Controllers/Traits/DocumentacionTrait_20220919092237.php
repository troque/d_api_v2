<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\Antecedente\AntecedenteResource;
use App\Http\Resources\DocumentoSirius\DocumentoSiriusResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\AntecedenteModel;
use App\Models\CompulsaModel;
use App\Models\DependenciaOrigenModel;
use App\Models\DocumentoSiriusModel;
use App\Models\GestorRespuestaModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\TbintDocumentoSiriusDescripcionModel;
use App\Models\User;
use App\Repositories\RepositoryGeneric;
use Illuminate\Support\Facades\DB;

trait DocumentacionTrait
{

    use MailTrait;

    /**
     * Sube y guarda la documentación
     */
    public static function storeDocumento($datosRequest)
    {
        try {
            $repository = new RepositoryGeneric();
            $repository->setModel(new DocumentoSiriusModel());

            DB::connection()->beginTransaction();

            $repository_tbint_documento_sirius_descripcion = new RepositoryGeneric();
            $repository_tbint_documento_sirius_descripcion->setModel(new TbintDocumentoSiriusDescripcionModel());

            $uuid_descripcion = null;
            $descripcion = null;
            $uuid_descripcion_compulsa = null;
            $descripcion_compulsa = null;
            $path = null;

            error_log("id_proceso_disciplinario----->: ".$datosRequest[0]['id_proceso_disciplinario']);

            // Consultar en la tabla log_proceso_disciplinario la etapa que está en estado remitido (que esta abierta)
            //$log_uuid = DB::select("select uuid from log_proceso_disciplinario where id_proceso_disciplinario = '".$datosRequest[0]['id_proceso_disciplinario']."' and id_estado = "+Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido']);

            $log_uuid = DB::select("select uuid from log_proceso_disciplinario where id_proceso_disciplinario = '".$datosRequest[0]['id_proceso_disciplinario']."'
             and id_tipo_log = ".Constants::TIPO_LOG['etapa']." and id_estado = ".Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido']);

            $datosRequest[0]['id_log_proceso_disciplinario'] = $log_uuid[0]->uuid;
            error_log("UUID----->: ".$datosRequest[0]['id_log_proceso_disciplinario']);


            foreach($datosRequest as $datos){
                if(isset($datos['es_compulsa']) && $datos['es_compulsa']){
                    $descripcion_compulsa = $datos['descripcion'];
                }
                else{
                    $descripcion = $datos['descripcion'];
                }
            }

            if($descripcion && $descripcion != null){
                $datos['created_user'] = auth()->user()->name;
                $datos['descripcion'] = $descripcion;
                $datos['id_proceso_disciplinario'] = $datosRequest[0]['id_proceso_disciplinario'];
                $result_tbint_documento_sirius_descripcion = $repository_tbint_documento_sirius_descripcion->create($datos);
                $uuid_descripcion = $result_tbint_documento_sirius_descripcion->uuid;
            }

            if($descripcion_compulsa && $descripcion_compulsa != null){
                $datos['created_user'] = auth()->user()->name;
                $datos['descripcion'] = $descripcion_compulsa;
                $datos['id_proceso_disciplinario'] = $datosRequest[0]['id_proceso_disciplinario'];
                $result_tbint_documento_sirius_descripcion = $repository_tbint_documento_sirius_descripcion->create($datos);
                $uuid_descripcion_compulsa = $result_tbint_documento_sirius_descripcion->uuid;
            }

            //Primero registra en Tabla: Documento Sirus



            foreach($datosRequest as $datos){

                $datos['id_log_proceso_disciplinario'] = $datosRequest[0]['id_log_proceso_disciplinario'];

                if(isset($datos['es_compulsa']) && $datos['es_compulsa']){
                    $datos['created_user'] = auth()->user()->name;
                    $datos['grupo'] = $uuid_descripcion_compulsa;
                    $result_insert_documento_sirus = $repository->create($datos);
                    $repository_compulsa = new RepositoryGeneric();
                    $repository_compulsa->setModel(new CompulsaModel());
                    $datos['radicado'] = $datos['num_radicado'];
                    $datos['id_documento_sirius'] = $result_insert_documento_sirus->uuid;
                    $datos['grupo'] = $uuid_descripcion_compulsa;
                    $repository_compulsa->create($datos);
                }
                else{
                    $datos['created_user'] = auth()->user()->name;
                    $datos['es_compulsa'] = false;
                    $datos['grupo'] = $uuid_descripcion;
                    $path = storage_path() . '/files' . '/' . $datos['vigencia'] . '/' . $datos['num_radicado'];
                    $datos['path'] = $path;

                    $result_insert_documento_sirus = $repository->create($datos);
                    /*$nombre_archivo['nombre_archivo'] = $result_insert_documento_sirus->uuid;
                    $this->repository->update($nombre_archivo, $result_insert_documento_sirus->uuid);*/

                    /*Guardar File*/
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    $path = $path . '/'.$result_insert_documento_sirus->uuid.'.'.$datos['extension'];
                    $b64 = $datos['file64'];
                    $bin = base64_decode($b64, true);
                    file_put_contents($path, $bin);
                    /*Guardar File*/
                }

            }

            //$log_descripcion = "Descripción: " . $descripcion;
            $log_descripcion = $descripcion;

            if($descripcion_compulsa != null){
                //$log_descripcion = "\n" . "Antecedentes: " . $descripcion_compulsa;
                $log_descripcion = $descripcion_compulsa;
            }

            // Guardar en el LOG
            $logRequest['id_proceso_disciplinario'] = $result_insert_documento_sirus->id_proceso_disciplinario;
            $logRequest['id_etapa'] = $result_insert_documento_sirus->id_etapa;
            $logRequest['id_fase'] =  $result_insert_documento_sirus->id_fase;
            $logRequest['id_tipo_log'] = 2;
            $logRequest['descripcion'] = substr($log_descripcion, 0, 4000);
            $logRequest['created_user'] = $result_insert_documento_sirus->created_user;
            $logRequest['id_estado'] = Constants::ESTADO_LOG_PROCESO_DISCIPLINARIO['remitido']; // Remisionado
            $logRequest['id_dependencia_origen'] = auth()->user()->id_dependencia;
            $logRequest['id_funcionario_actual'] = auth()->user()->name;
            $logRequest['id_funcionario_registra'] = auth()->user()->name;

            if($datosRequest[0]['id_etapa'] == 2 && $datosRequest[0]['id_fase'] == 8){

                //BUSCAR JEFE DE LA DEPENDENCIA
                $repository_dependencia_origen = new RepositoryGeneric();
                $repository_dependencia_origen->setModel(new DependenciaOrigenModel());
                $resultado_dependencia = $repository_dependencia_origen->find(auth()->user()->id_dependencia);

                //BUSCAR USUARIO
                $repository_usuario = new RepositoryGeneric();
                $repository_usuario->setModel(new User());
                $resultado_usuario = $repository_usuario->find($resultado_dependencia->id_usuario_jefe);

                MailTrait::sendMail(
                    [$resultado_usuario->email],
                    $resultado_usuario->name,
                    "SINPROC: (" . $datosRequest[0]['num_radicado'] . ") - VIGENCIA (" . $datosRequest[0]['vigencia']. ')',
                    'Se realizo la fase de DOCUMENTO CIERRE de la etapa de evaluación',
                    [$path],
                    null,
                    null,
                );

                $logModel = new LogProcesoDisciplinarioModel();
                LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
                $respuesta = DocumentoSiriusResource::make($result_insert_documento_sirus);
            }
            if($datosRequest[0]['id_etapa'] == 2 && $datosRequest[0]['id_fase'] == 9){

                $repository_gestor_respuesta = new RepositoryGeneric();
                $repository_gestor_respuesta->setModel(new GestorRespuestaModel());
                $datos_gestor_respuesta['id_proceso_disciplinario'] = $datosRequest[0]['id_proceso_disciplinario'];
                $datos_gestor_respuesta['aprobado'] = true;
                $datos_gestor_respuesta['nuevo_documento'] = true;
                $datos_gestor_respuesta['created_user'] = auth()->user()->name;
                $datos_gestor_respuesta['descripcion'] = $datosRequest[0]['id_proceso_disciplinario'];
                $datos_gestor_respuesta['proceso_finalizado'] = false;

                $respuesta_gestor_respuesta = $repository_gestor_respuesta->customQuery(
                    function ($model) use ($datos_gestor_respuesta){
                        return $model
                        ->where('id_proceso_disciplinario', $datos_gestor_respuesta['id_proceso_disciplinario'])
                        ->orderby('created_at', 'desc')
                        ->get();
                    }
                )->first();

                if($respuesta_gestor_respuesta){
                    $datos_gestor_respuesta['version'] = $respuesta_gestor_respuesta->version + 1;
                }
                else{
                    $datos_gestor_respuesta['version'] = 1;
                }

                $repository_gestor_respuesta->create($datos_gestor_respuesta);

                $logModel = new LogProcesoDisciplinarioModel();
                LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
                $respuesta = DocumentoSiriusResource::make($result_insert_documento_sirus);
            }
            else{
                $logModel = new LogProcesoDisciplinarioModel();
                LogProcesoDisciplinarioResource::make($logModel->create($logRequest));
                $respuesta = DocumentoSiriusResource::make($result_insert_documento_sirus);
            }

            DB::connection()->commit();
            return "realizado";

        } catch (\Exception $e) {
            error_log($e);
            return $e;
        }
    }
}

