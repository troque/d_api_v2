<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\LogTrait;
use App\Http\Controllers\Traits\MigracionesTrait;
use App\Http\Controllers\Traits\ReclasificacionTrait;
use App\Http\Requests\BuscadorFormRequest;
use App\Http\Resources\Actuaciones\ActuacionesResource;
use App\Http\Resources\Antecedente\AntecedenteResource;
use App\Http\Resources\CierreEtapa\CierreEtapaResource;
use App\Http\Resources\ClasificacionRadicado\ClasificacionRadicadoResource;
use App\Http\Resources\ComunicacionInteresado\ComunicacionInteresadoResource;
use App\Http\Resources\DatosInteresado\DatosInteresadoResource;
use App\Http\Resources\DocumentoCierre\DocumentoCierreResource;
use App\Http\Resources\EntidadInvestigado\EntidadInvestigadoResource;
use App\Http\Resources\Evaluacion\EvaluacionResource;
use App\Http\Resources\GestorRespuesta\GestorRespuestaResource;
use App\Http\Resources\InformeCierre\InformeCierreResource;
use App\Http\Resources\ProcesoDesglose\ProcesoDesgloseResource;
use App\Http\Resources\ProcesoDiciplinario\ProcesoDiciplinarioResource;
use App\Http\Resources\ProcesoSinproc\ProcesoSinprocResource;
use App\Http\Resources\ProcesoSirius\ProcesoSiriusResource;
use App\Http\Resources\RemisionQueja\RemisionQuejaResource;
use App\Http\Resources\ValidarClasificacion\ValidarClasificacionResource;
use App\Http\Utilidades\Constants;
use App\Models\ActuacionesModel;
use App\Models\AntecedenteModel;
use App\Models\CierreEtapaModel;
use App\Models\ClasificacionRadicadoModel;
use App\Models\ComunicacionInteresadoModel;
use App\Models\DatosInteresadoModel;
use App\Models\DocumentoCierreModel;
use App\Models\EntidadInvestigadoModel;
use App\Models\EvaluacionModel;
use App\Models\GestorRespuestaModel;
use App\Models\InformeCierreModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Models\ProcesoDesgloseModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\ProcesoSinprocModel;
use App\Models\ProcesoSiriusModel;
use App\Models\RemisionQuejaModel;
use App\Models\ValidarClasificacionModel;
use App\Repositories\RepositoryGeneric;
use Exception;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class MigracionController extends Controller
{
    use MigracionesTrait;
    use LogTrait;
    use ReclasificacionTrait;

    public function actualizarFechaFromDisciplinarios()
    {

        $query_disc_solicitudes = DB::select("SELECT num_solicitud, vigencia, tabla FROM DISC_SOLICITUDES");
        $cant_total = DB::select("SELECT count(*) as total FROM DISC_SOLICITUDES");
        $total = $cant_total[0]->total;

        $repository_temp_proceso_disciplinario = new RepositoryGeneric();
        $repository_temp_proceso_disciplinario->setModel(new ClasificacionRadicadoModel());

        for ($cont = 0; $cont < $total; $cont++) {

            $num = $cont + 1;

            $proceso = DB::select("SELECT
            num_solicitud,
            vigencia,
            fecha_registro,
            fecha_ingreso_dependencia,
            id_dependencia,
            estado,
            id_tramite
            FROM " . $query_disc_solicitudes[$cont]->tabla .
                " WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud .
                " AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia);


            $existe_proceso = DB::select("SELECT uuid FROM proceso_disciplinario WHERE radicado = '" . $query_disc_solicitudes[$cont]->num_solicitud . "' AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia);

            if (count($existe_proceso) > 0) {

                ProcesoDiciplinarioModel::where('uuid', $existe_proceso[0]->uuid)->update(['created_at' => $proceso[0]->fecha_ingreso_dependencia, 'updated_at' => $proceso[0]->fecha_ingreso_dependencia]);
                LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $existe_proceso[0]->uuid)->update(['created_at' => $proceso[0]->fecha_ingreso_dependencia, 'updated_at' => $proceso[0]->fecha_ingreso_dependencia]);

                error_log($num . ' DE ' . $total . ' --> ' . $query_disc_solicitudes[$cont]->num_solicitud . ' - ' . $query_disc_solicitudes[$cont]->vigencia . ' - ' . $proceso[0]->fecha_ingreso_dependencia . " - ACTUALIZADO");

                DB::connection()->commit();
            }
        }
    }


    /**
     * El método $this->getDependenciaAmbientePruebas solo debe usarse en ambiente de pruebas
     */
    public function iniciarMigracionFromDisciplinarios()
    {


        //$query_disc_solicitudes = DB::select("SELECT num_solicitud, vigencia, tabla FROM DISC_SOLICITUDES WHERE num_solicitud  IN (33881, 34527,37987,38312,321676,322805,328835,328940,335851,336162,337246,337246,342199,344557, 354292,355254,356190,369519,372064,382897,390477,3385405,3477137,3495433,3500531)");
        //$query_disc_solicitudes = DB::select("SELECT num_solicitud, vigencia, tabla FROM DISC_SOLICITUDES");

        $query_disc_solicitudes = DB::select("SELECT num_solicitud, vigencia, tabla FROM DISC_SOLICITUDES WHERE num_solicitud  IN (1	,
                        13751	,
                        14706	,
                        16719	,
                        17085	,
                        17815	,
                        18284	,
                        21885	,
                        31988	,
                        31993	,
                        32231	,
                        32293	,
                        34377	,
                        36045	,
                        36663	,
                        133843	,
                        134583	,
                        140687	,
                        146933	,
                        149751	,
                        151609	,
                        151773	,
                        152896	,
                        153206	,
                        154414	,
                        163854	,
                        165235	,
                        166657	,
                        167225	,
                        172890	,
                        178118	,
                        185840	,
                        185957	,
                        187479	,
                        191174	,
                        193550	,
                        193815	,
                        194741	,
                        196232	,
                        196334	,
                        197740	,
                        198651	,
                        203169	,
                        204883	,
                        205075	,
                        206058	,
                        206582	,
                        206589	,
                        214930	,
                        230487	,
                        230498	,
                        230586	,
                        233180	,
                        234030	,
                        241967	,
                        245465	,
                        246104	,
                        258156	,
                        258411	,
                        260589	,
                        262351	,
                        263143	,
                        265067	,
                        266362	,
                        267546	,
                        267990	,
                        271813	,
                        276034	,
                        283339	,
                        300375	,
                        301787	,
                        303017	,
                        308206	,
                        311040	,
                        314296	,
                        318440	,
                        322849	,
                        342900	,
                        367611	,
                        382151	,
                        386788	,
                        388721	,
                        391976	,
                        854975	,
                        2757975	,
                        2764675	,
                        2796316	,
                        2836506	,
                        2918563	,
                        2918564	,
                        2918906	,
                        2924042	,
                        2929382	,
                        2931476	,
                        2943497	,
                        2943563	,
                        2943574	,
                        2943583	,
                        2943599	,
                        2944060	,
                        2944062	,
                        2946308	,
                        2956061	,
                        2960527	,
                        2962413	,
                        2969600	,
                        2969622	,
                        2969623	,
                        2969624	,
                        2969625	,
                        2969626	,
                        2970660	,
                        2974348	,
                        3014124	,
                        3098546	,
                        3173775	,
                        3177258	,
                        3198602	,
                        3202271	,
                        3202395	,
                        3212382	,
                        3213486	,
                        3213489	,
                        3213491	,
                        3218235	,
                        3218626	,
                        3218630	,
                        3218635	,
                        3218641	,
                        3218650	,
                        3218656	,
                        3218662	,
                        3218663	,
                        3218666	,
                        3218668	,
                        3218835	,
                        3218843	,
                        3229020	,
                        3247235	,
                        3247241	,
                        3249113	,
                        3249223	,
                        3249226	,
                        3249297	,
                        3249299	,
                        3265601	,
                        3278432	,
                        3280790	,
                        3280798	,
                        3280805	,
                        3280812	,
                        3306199	,
                        3320025	,
                        3383573	,
                        3392592	,
                        3444344	,
                        3497611	,
                        3514550	,
                        3547243	,
                        3858634	,
                        3861185	)");

        /* $query_disc_solicitudes = DB::select("SELECT num_solicitud, vigencia, tabla
        FROM (
          SELECT num_solicitud, vigencia, tabla, ROW_NUMBER() OVER (ORDER BY ROWNUM DESC) AS rn
          FROM DISC_SOLICITUDES
        )
        ORDER BY rn");*/

        $total = count($query_disc_solicitudes);

        for ($cont = 0; $cont < $total; $cont++) {

            //try {

            $num = $cont + 1;

            $proceso = DB::select("SELECT
                    num_solicitud,
                    vigencia,
                    fecha_registro,
                    fecha_ingreso_dependencia,
                    id_dependencia,
                    estado,
                    id_tramite
                    FROM " . $query_disc_solicitudes[$cont]->tabla .
                " WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud .
                " AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia);


            $etapa = DB::select("SELECT
                    id_etapa, dependencia_finaliza
                    FROM DISC_CIERRE_ETAPAS
                    WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud . "
                    AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia . "
                    AND id_etapa = 1");

            $estado_proceso_a = DB::select("SELECT
                    estado_tramite
                    FROM TRAMITEUSUARIO WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud .
                " AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia . " AND id_tramite = " . $proceso[0]->id_tramite . " AND estado_tramite = 'Finalizado'");

            $estado_proceso_b = DB::select("SELECT
                    estado_tramite
                    FROM TRAMITERESPUESTA WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud .
                " AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia . " AND id_tramite = " . $proceso[0]->id_tramite . " AND num_paso = 20 AND estado_tramite = 'Finalizado' order by FEC_RESPUESTA DESC");

            if (count($estado_proceso_a) > 0  && count($estado_proceso_b) > 0) {
                $proceso[0]->estado = 2; // CERRADO
            } else {
                $proceso[0]->estado = 1; // ABIERTO
            }


            $existe_proceso = DB::select("SELECT radicado FROM proceso_disciplinario WHERE radicado = '" . $query_disc_solicitudes[$cont]->num_solicitud . "' AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia);

            if (count($existe_proceso) > 0) {
                error_log($num . ' DE ' . $total . ' --> ' . $query_disc_solicitudes[$cont]->num_solicitud . ' - ' . $query_disc_solicitudes[$cont]->vigencia . " - YA FUE REGISTRADO");
            } else {
                error_log($num . ' DE ' . $total . ' --> ' . $query_disc_solicitudes[$cont]->num_solicitud . ' - ' . $query_disc_solicitudes[$cont]->vigencia .  " - SE REGISTRA");
            }


            if (count($etapa) > 0 && count($existe_proceso) == 0) {

                if ($query_disc_solicitudes[$cont]->tabla == 'DISC_INGRESO_DESGLOSE') {
                    $datosProcesoDisciplinario['id_tipo_proceso'] = Constants::TIPO_DE_PROCESO['desglose'];
                } else if ($query_disc_solicitudes[$cont]->tabla == 'DISC_INGRESO_CORRESPONDENCIA') {
                    $datosProcesoDisciplinario['id_tipo_proceso'] = Constants::TIPO_DE_PROCESO['correspondencia_sirius'];
                } else if ($query_disc_solicitudes[$cont]->tabla == 'DISC_INGRESO_SINPROC') {
                    $datosProcesoDisciplinario['id_tipo_proceso'] = Constants::TIPO_DE_PROCESO['sinproc'];
                }

                // INICIO SOLO APLICA PARA PRUEBAS DEBE ELIMINARSE CUANDO SE PASE A PRODUCCION
                /*$proceso[0]->id_dependencia = $this->getDependenciaAmbientePruebas($proceso[0]->id_dependencia);
                $etapa[0]->dependencia_finaliza =  $this->getDependenciaAmbientePruebas($etapa[0]->dependencia_finaliza);*/
                // FIN SOLO APLICA PARA PRUEBAS DEBE ELIMINARSE CUANDO SE PASE A PRODUCCION

                $proceso[0]->id_dependencia = $proceso[0]->id_dependencia;
                $etapa[0]->dependencia_finaliza = $etapa[0]->dependencia_finaliza;


                // REGISTRAR PROCESO DISCIPLINARIO
                $datosProcesoDisciplinario['radicado'] = $proceso[0]->num_solicitud;
                $datosProcesoDisciplinario['vigencia'] = $proceso[0]->vigencia;
                $datosProcesoDisciplinario['vigencia_origen'] = $proceso[0]->vigencia;
                $datosProcesoDisciplinario['estado'] =  $proceso[0]->estado;
                $datosProcesoDisciplinario['created_user'] = auth()->user()->name;
                $datosProcesoDisciplinario['update_user'] = auth()->user()->name;
                $datosProcesoDisciplinario['created_at'] = $proceso[0]->fecha_ingreso_dependencia;
                $datosProcesoDisciplinario['fecha_ingreso'] = $proceso[0]->fecha_ingreso_dependencia;
                $datosProcesoDisciplinario['id_etapa'] = $etapa[0]->id_etapa;
                $datosProcesoDisciplinario['id_dependencia'] = $proceso[0]->id_dependencia;
                /*$datosProcesoDisciplinario['id_dependencia_actual'] = $this->getDependenciaAmbientePruebas($etapa[0]->dependencia_finaliza);
                $datosProcesoDisciplinario['id_dependencia_duena'] = $this->getDependenciaAmbientePruebas($etapa[0]->dependencia_finaliza);*/
                $datosProcesoDisciplinario['id_dependencia_actual'] = $etapa[0]->dependencia_finaliza;
                $datosProcesoDisciplinario['id_dependencia_duena'] = $etapa[0]->dependencia_finaliza;
                $datosProcesoDisciplinario['migrado'] =  true;
                $datosProcesoDisciplinario['fuente_bd'] =  true;
                $datosProcesoDisciplinario['fuente_excel'] =  false;
                $datosProcesoDisciplinario['id_tramite_usuario'] =  $proceso[0]->id_tramite;

                if ($query_disc_solicitudes[$cont]->tabla == 'DISC_INGRESO_DESGLOSE') {

                    $proceso_d = DB::select("SELECT
                    auto_numero,
                    num_solicitud_padre,
                    id_dependencia_origen,
                    fecha_auto
                    FROM " . $query_disc_solicitudes[$cont]->tabla .
                        " WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud .
                        " AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia);

                    $datosProcesoDisciplinario['numero_auto'] =  $proceso_d[0]->auto_numero;
                    $datosProcesoDisciplinario['radicado_padre'] =  $proceso_d[0]->num_solicitud_padre;
                    $datosProcesoDisciplinario['fecha_auto_desglose'] =  $proceso_d[0]->fecha_auto;
                    $datosProcesoDisciplinario['id_dependencia_origen'] =  $proceso_d[0]->id_dependencia_origen;
                }

                $model_proceso_disciplinario = new ProcesoDiciplinarioModel();
                $rta_proceso = ProcesoDiciplinarioResource::make($model_proceso_disciplinario->create($datosProcesoDisciplinario));
                $array_proceso = json_decode(json_encode($rta_proceso));

                $copyProcesoDisciplinario =  $datosProcesoDisciplinario;
                $copyProcesoDisciplinario['uuid'] =  $rta_proceso->uuid;

                if ($query_disc_solicitudes[$cont]->tabla == 'DISC_INGRESO_DESGLOSE') {

                    $model_proceso_disciplinario = new ProcesoDesgloseModel();
                    $rta_proceso = ProcesoDesgloseResource::make($model_proceso_disciplinario->create($copyProcesoDisciplinario));
                } else if ($query_disc_solicitudes[$cont]->tabla == 'DISC_INGRESO_CORRESPONDENCIA') {

                    $_proceso_sirius = DB::select("SELECT
                            radicado_entidad_externa
                            FROM " . $query_disc_solicitudes[$cont]->tabla .
                        " WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud .
                        " AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia);

                    if (count($_proceso_sirius) > 0) {
                        $copyProcesoDisciplinario['radicado_entidad'] =  $_proceso_sirius[0]->radicado_entidad_externa;
                    }
                    $model_proceso_disciplinario = new ProcesoSiriusModel();
                    $rta_proceso = ProcesoSiriusResource::make($model_proceso_disciplinario->create($copyProcesoDisciplinario));
                } else if ($query_disc_solicitudes[$cont]->tabla == 'DISC_INGRESO_SINPROC') {
                    $model_proceso_disciplinario = new ProcesoSinprocModel();
                    $rta_proceso = ProcesoSinprocResource::make($model_proceso_disciplinario->create($copyProcesoDisciplinario));
                }

                //REGISTRAR EN EL LOG PROCESO DISCIPLINARIO
                $this->storeLogMigracion($etapa[0]->id_etapa, $array_proceso->id, $proceso[0]->id_dependencia, Constants::FASE['inicio_proceso_disciplinario'], $array_proceso->id, auth()->user()->name, $datosProcesoDisciplinario['created_at']);


                // REGISTRAR ANTECEDENTES
                $antecedentes = DB::select("SELECT
                        descripcion, fecha_ingreso, id_dependencia, consecutivo, id_etapa_actual
                        FROM DISC_ANTECEDENTES_E1
                        WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud . "
                        AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia . "
                        ORDER BY consecutivo ASC");

                $total_antecedentes = count($antecedentes);

                if ($total_antecedentes > 0) {

                    for ($cont_antecedente = 0; $cont_antecedente < $total_antecedentes; $cont_antecedente++) {

                        if ($antecedentes[$cont_antecedente]->id_etapa_actual == null) {
                            $antecedentes[$cont_antecedente]->id_etapa_actual = 1;
                        }

                        $datosAntecedente['descripcion'] = $antecedentes[$cont_antecedente]->descripcion;
                        $datosAntecedente['fecha_registro'] = $antecedentes[$cont_antecedente]->fecha_ingreso;
                        //$datosAntecedente['id_dependencia'] = $this->getDependenciaAmbientePruebas($antecedentes[$cont_antecedente]->id_dependencia);
                        $datosAntecedente['id_dependencia'] = $antecedentes[$cont_antecedente]->id_dependencia;
                        $datosAntecedente['estado'] =  1;
                        $datosAntecedente['id_proceso_disciplinario'] = $array_proceso->id;
                        $datosAntecedente['id_etapa'] = $antecedentes[$cont_antecedente]->id_etapa_actual;
                        $datosAntecedente['created_user'] = auth()->user()->name;
                        $datosAntecedente['created_at'] = strtotime($antecedentes[$cont_antecedente]->fecha_ingreso);

                        $model_antecedente = new AntecedenteModel();
                        $rta_antecedentes = AntecedenteResource::make($model_antecedente->create($datosAntecedente));
                        $array_antecedentes = json_decode(json_encode($rta_antecedentes));


                        if ($array_antecedentes != null) {
                            $this->storeLogMigracion($etapa[0]->id_etapa, $array_proceso->id, $antecedentes[$cont_antecedente]->id_dependencia, Constants::FASE['antecedentes'], $array_antecedentes->id, auth()->user()->name, $datosProcesoDisciplinario['created_at']);
                        }
                    }
                }

                // DATOS DEL INTERESADO QUEJOSO
                $solicitud_quejoso = DB::select("SELECT
                            id_quejoso
                            FROM DISC_SOLICITUD_QUEJOSO
                            WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud . "
                            AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia);

                $total_quejoso = count($solicitud_quejoso);

                if ($total_quejoso > 0) {

                    for ($cont_quejoso = 0; $cont_quejoso < $total_quejoso; $cont_quejoso++) {

                        $quejoso = DB::select("SELECT
                                    id_persona,
                                    id_empresa,
                                    estado,
                                    fecha,
                                    direccion,
                                    telefono1,
                                    telefono2,
                                    correo,
                                    diligenciadopor,
                                    id_dependencia_funcionario
                                    FROM DISC_QUEJOSO
                                    WHERE id_quejoso = " . $solicitud_quejoso[$cont_quejoso]->id_quejoso);

                        if (count($quejoso) > 0) {

                            $persona = DB::select("SELECT
                                    id_tipo_documento,
                                    primer_nombre,
                                    segundo_nombre,
                                    primer_apellido,
                                    segundo_apellido,
                                    numero_documento,
                                    folio
                                    FROM DISC_PERSONA
                                    WHERE id_persona = " . $quejoso[0]->id_persona);

                            if (count($persona) > 0) {

                                if ($persona[0]->id_tipo_documento == 'CC') {
                                    $persona[0]->id_tipo_documento = Constants::TIPO_DOCUMENTO['cedula_ciudadania'];
                                } else if ($persona[0]->id_tipo_documento == 'CE') {
                                    $persona[0]->id_tipo_documento = Constants::TIPO_DOCUMENTO['cedula_extranjeria'];
                                } else if ($persona[0]->id_tipo_documento == 'PE') {
                                    $persona[0]->id_tipo_documento = Constants::TIPO_DOCUMENTO['pasaporte'];
                                } else if ($persona[0]->id_tipo_documento == 'NI') {
                                    $persona[0]->id_tipo_documento = Constants::TIPO_DOCUMENTO['no_informa'];
                                } else {
                                    $persona[0]->id_tipo_documento = Constants::TIPO_DOCUMENTO['no_informa'];
                                }


                                if ($quejoso[0]->id_empresa != 0) {

                                    $empresa = DB::select("SELECT
                                            id_tipo_empresa,
                                            id_entidad_publica,
                                            id_dependiencia_personeria
                                            FROM DISC_EMPRESA
                                            WHERE id_empresa = " . $quejoso[0]->id_empresa);

                                    if (count($empresa) > 0) {
                                        $datosInteresado['id_tipo_entidad'] = $empresa[0]->id_tipo_empresa;
                                        $datosInteresado['id_entidad'] = $empresa[0]->id_entidad_publica;

                                        if ($empresa[0]->id_dependiencia_personeria == null) {
                                            $datosInteresado['id_dependencia_entidad'] = 9999;
                                            $datosInteresado['id_dependencia'] = 9999;
                                        } else {
                                            $datosInteresado['id_dependencia_entidad'] =  $empresa[0]->id_dependiencia_personeria;
                                            $datosInteresado['id_dependencia'] =  $empresa[0]->id_dependiencia_personeria;
                                        }
                                    }
                                }

                                $datosInteresado['id_etapa'] = Constants::ETAPA['captura_reparto'];;
                                $datosInteresado['id_tipo_interesao'] = $quejoso[0]->id_empresa == 0 ? Constants::TIPO_INTERESADO['persona_natural'] : Constants::TIPO_INTERESADO['entidad'];
                                $datosInteresado['id_proceso_disciplinario'] = $array_proceso->id;
                                $datosInteresado['id_tipo_sujeto_procesal'] = Constants::TIPO_SUJETO_PROCESAL['interesado'];
                                $datosInteresado['tipo_documento'] = $persona[0]->id_tipo_documento;
                                $datosInteresado['numero_documento'] = $persona[0]->numero_documento;
                                $datosInteresado['primer_nombre'] = $persona[0]->primer_nombre;
                                $datosInteresado['segundo_nombre'] = $persona[0]->segundo_nombre;
                                $datosInteresado['primer_apellido'] = $persona[0]->primer_apellido;
                                $datosInteresado['segundo_apellido'] = $persona[0]->segundo_apellido;
                                $datosInteresado['telefono_fijo'] = $quejoso[0]->telefono1;
                                $datosInteresado['telefono_celular'] = $quejoso[0]->telefono2;
                                $datosInteresado['direccion'] = $quejoso[0]->direccion;
                                $datosInteresado['email'] = $quejoso[0]->correo;
                                $datosInteresado['primer_apellido'] = $persona[0]->primer_apellido;
                                $datosInteresado['created_user'] = auth()->user()->name;
                                $datosInteresado['estado'] = 1;
                                $datosInteresado['folio'] = $persona[0]->folio;
                                $datosInteresado['id_funcionario'] = auth()->user()->id;
                                $datosInteresado['autorizar_envio_correo'] = 0;
                                $datosInteresado['created_at'] =  $quejoso[0]->fecha;

                                $model_interesado = new DatosInteresadoModel();
                                $rta_interesado = DatosInteresadoResource::make($model_interesado->create($datosInteresado));
                                $array_interesado = json_decode(json_encode($rta_interesado));

                                //REGISTRAR EN EL LOG PROCESO DISCIPLINARIO
                                $this->storeLogMigracion(Constants::ETAPA['captura_reparto'], $array_proceso->id, $this->getDependenciaAmbientePruebas($proceso[0]->id_dependencia), Constants::FASE['datos_interesado'], $array_interesado->id, auth()->user()->name, $datosProcesoDisciplinario['created_at']);
                            }
                        }
                    }
                }

                // CLASIFICACION DEL RADICADO
                $clasificacion_radicado = DB::select("SELECT
                            tipo_expediente,
                            termino_tutela_d,
                            termino_tutela_h,
                            id_dependencia,
                            observacion_derecho_pet,
                            id_etapa,
                            fecha_registro,
                            estado_reparto
                            FROM DISC_EXPEDIENTE_A_REPARTO
                            WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud . "
                            AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia . " ORDER BY id_etapa ASC");

                $total_clasificacion = count($clasificacion_radicado);

                if ($total_clasificacion > 0) {

                    for ($cont_clasificacion = 0; $cont_clasificacion < $total_clasificacion; $cont_clasificacion++) {

                        if ($clasificacion_radicado[$cont_clasificacion]->tipo_expediente == 3) { // TUTELA
                            $datosClasificacion['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['tutela'];
                            if ($clasificacion_radicado[$cont_clasificacion]->termino_tutela_d != null) {
                                $datosClasificacion['id_termino_respuesta'] = Constants::TIPO_TUTELA['dias'];
                                $datosClasificacion['fecha_termino'] = $clasificacion_radicado[$cont_clasificacion]->termino_tutela_d;
                            } else if ($clasificacion_radicado[$cont_clasificacion]->termino_tutela_h != null) {
                                $datosClasificacion['id_termino_respuesta'] = Constants::TIPO_TUTELA['horas'];
                                $datosClasificacion['hora_termino'] = $clasificacion_radicado[$cont_clasificacion]->termino_tutela_h;
                            }
                        } else if ($clasificacion_radicado[$cont_clasificacion]->tipo_expediente == 4) { //  PODER PREFERENTE
                            $datosClasificacion['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['poder_referente'];
                            $datosClasificacion['id_tipo_queja'] = Constants::TIPO_QUEJA['externa'];
                        } else if ($clasificacion_radicado[$cont_clasificacion]->tipo_expediente == 210) { //  QUEJA INTERNA
                            $datosClasificacion['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['queja'];
                            $datosClasificacion['oficina_control_interno'] = true;
                            $datosClasificacion['id_tipo_queja'] = Constants::TIPO_QUEJA['interna'];
                        } else if ($clasificacion_radicado[$cont_clasificacion]->tipo_expediente == 220) { //  QUEJA EXTERNA
                            $datosClasificacion['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['queja'];
                            $datosClasificacion['id_tipo_queja'] = Constants::TIPO_QUEJA['externa'];
                        } else if ($clasificacion_radicado[$cont_clasificacion]->tipo_expediente == 101) { //  DERECHO DE PETICION COPIAS
                            $datosClasificacion['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['derecho_peticion'];
                            $datosClasificacion['id_tipo_derecho_peticion'] = Constants::TIPO_DERECHO_PETICION['copias'];
                        } else if ($clasificacion_radicado[$cont_clasificacion]->tipo_expediente == 103) { //  DERECHO DE PETICION ALERTA CONTROL POLITICO
                            $datosClasificacion['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['derecho_peticion'];
                            $datosClasificacion['id_tipo_derecho_peticion'] = Constants::TIPO_DERECHO_PETICION['alerta_control_politico'];
                            $datosClasificacion['gestion_juridica'] = false;
                        } else if ($clasificacion_radicado[$cont_clasificacion]->tipo_expediente == 102) { //  DERECHO DE PETICION GENERAL
                            $datosClasificacion['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['derecho_peticion'];
                            $datosClasificacion['id_tipo_derecho_peticion'] = Constants::TIPO_DERECHO_PETICION['general'];
                        } else if ($clasificacion_radicado[$cont_clasificacion]->tipo_expediente == 104 || $clasificacion_radicado[$cont_clasificacion]->tipo_expediente == 105) { //  DERECHO DE PETICION ALERTA CONTROL POLITICO SIN INTERVENCION JURIDICA
                            $datosClasificacion['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['derecho_peticion'];
                            $datosClasificacion['id_tipo_derecho_peticion'] = Constants::TIPO_DERECHO_PETICION['alerta_control_politico'];
                            $datosClasificacion['gestion_juridica'] = true;
                        } else if ($clasificacion_radicado[$cont_clasificacion]->tipo_expediente == 7) { //  PROCESO DISCIPLINARIO
                            $datosClasificacion['id_tipo_expediente'] = Constants::TIPO_EXPEDIENTE['proceso_disciplinario'];
                            $datosClasificacion['id_tipo_derecho_peticion'] = Constants::TIPO_QUEJA['externa'];
                            $datosClasificacion['gestion_juridica'] = true;
                        }

                        if ($total_clasificacion == 2) {
                            if ($cont_clasificacion == 0) {
                                $datosClasificacion['estado'] = 0;
                            } else {
                                $datosClasificacion['estado'] = 1;
                            }
                        } else if ($total_clasificacion == 1) {
                            $datosClasificacion['estado'] = 1;
                        }

                        $datosClasificacion['id_etapa'] =  $clasificacion_radicado[$cont_clasificacion]->id_etapa;
                        $datosClasificacion['id_proceso_disciplinario'] = $array_proceso->id;
                        $datosClasificacion['validacion_jefe'] = true;
                        $datosClasificacion['observaciones'] = $clasificacion_radicado[$cont_clasificacion]->observacion_derecho_pet;
                        $datosClasificacion['created_user'] = auth()->user()->name;
                        $datosClasificacion['created_at'] = $clasificacion_radicado[$cont_clasificacion]->fecha_registro;
                        $datosClasificacion['id_dependencia'] = $this->getDependenciaAmbientePruebas($clasificacion_radicado[$cont_clasificacion]->id_dependencia);
                        $datosClasificacion['reclasificacion'] = false;

                        $model_clasificacion = new ClasificacionRadicadoModel();
                        $rta_clasificacion = ClasificacionRadicadoResource::make($model_clasificacion->create($datosClasificacion));
                        $array_clasificacion = json_decode(json_encode($rta_clasificacion));

                        //REGISTRAR EN EL LOG PROCESO DISCIPLINARIO
                        $this->storeLogMigracion($clasificacion_radicado[$cont_clasificacion]->id_etapa, $array_proceso->id, $proceso[0]->id_dependencia, Constants::FASE['clasificacion_radicado'], $array_clasificacion->id, auth()->user()->name, $datosProcesoDisciplinario['created_at']);

                        if ($datosClasificacion['id_etapa'] == 2) {

                            $datosValidarClasificacion['id_clasificacion_radicado'] =  $array_clasificacion->id;
                            $datosValidarClasificacion['estado'] = 1;
                            $datosValidarClasificacion['id_etapa'] = Constants::ETAPA['evaluacion'];
                            $datosValidarClasificacion['id_proceso_disciplinario'] = $array_proceso->id;
                            $datosValidarClasificacion['eliminado'] = 0;
                            $datosValidarClasificacion['created_user'] = auth()->user()->name;
                            $datosValidarClasificacion['created_at'] = $datosClasificacion['created_at'];

                            $model_validar_clasificacion = new ValidarClasificacionModel();
                            $rta_validar_clasificacion = ValidarClasificacionResource::make($model_validar_clasificacion->create($datosValidarClasificacion));
                            $array_validar_clasificacion = json_decode(json_encode($rta_validar_clasificacion));

                            //REGISTRAR EN EL LOG PROCESO DISCIPLINARIO
                            $this->storeLogMigracion(Constants::ETAPA['evaluacion'], $array_proceso->id, $this->getDependenciaAmbientePruebas($proceso[0]->id_dependencia), Constants::FASE['validacion_clasificacion'], $array_validar_clasificacion->id, auth()->user()->name, $datosProcesoDisciplinario['created_at']);

                            ClasificacionRadicadoModel::where('uuid', $datosValidarClasificacion['id_clasificacion_radicado'])->update(['reclasificacion' => true]);
                        }
                    }
                }

                //REGISTRAR ENTIDAD DEL INVESTIGADO
                $entidad_investigado = DB::select("SELECT
                            id_entidad,
                            id_sector,
                            fecha_ingreso,
                            id_dependencia,
                            id_etapa_actual,
                            nombre_investigado,
                            cargo_investigado,
                            observaciones
                            FROM DISC_ENTIDAD_INVESTIGADO_E1
                            WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud . "
                            AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia);

                $total_entidad = count($entidad_investigado);

                if ($total_entidad > 0) {

                    for ($cont_entidad = 0; $cont_entidad < $total_entidad; $cont_entidad++) {

                        $datosEntidad['id_proceso_disciplinario'] = $array_proceso->id;
                        $datosEntidad['id_etapa'] =  $entidad_investigado[$cont_entidad]->id_etapa_actual;
                        $datosEntidad['nombre_investigado'] =  $entidad_investigado[$cont_entidad]->nombre_investigado;
                        $datosEntidad['cargo'] =  $entidad_investigado[$cont_entidad]->cargo_investigado;
                        $datosEntidad['created_user'] = auth()->user()->name;
                        $datosEntidad['created_at'] = $entidad_investigado[$cont_entidad]->fecha_ingreso;
                        $datosEntidad['observaciones'] = $entidad_investigado[$cont_entidad]->observaciones;
                        $datosEntidad['estado'] = true;

                        $model_entidad = new EntidadInvestigadoModel();
                        $rta_entidad = EntidadInvestigadoResource::make($model_entidad->create($datosEntidad));
                        $array_entidad = json_decode(json_encode($rta_entidad));

                        //REGISTRAR EN EL LOG PROCESO DISCIPLINARIO
                        $this->storeLogMigracion($datosEntidad['id_etapa'], $array_proceso->id, $this->getDependenciaAmbientePruebas($proceso[0]->id_dependencia), Constants::FASE['entidad_investigado'], $array_entidad->id, auth()->user()->name, $datosProcesoDisciplinario['created_at']);
                    }
                }

                //REGISTRAR CERRAR ETAPA CAPTURA Y REPARTO
                $cerrar_etapa_captura_reparto = DB::select("SELECT
                            id_etapa,
                            fecha_cierre,
                            dependencia_finaliza,
                            detalle_cierre
                            FROM DISC_CIERRE_ETAPAS
                            WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud . "
                            AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia . " AND id_etapa = 1");

                $datosCierreEtapa['id_etapa'] =  Constants::ETAPA['captura_reparto'];
                $datosCierreEtapa['id_proceso_disciplinario'] = $array_proceso->id;
                $datosCierreEtapa['created_user'] = auth()->user()->name;
                $datosCierreEtapa['id_funcionario_asignado'] = auth()->user()->name;
                $datosCierreEtapa['eliminado'] = false;
                $datosCierreEtapa['created_at'] = $cerrar_etapa_captura_reparto[0]->fecha_cierre;

                $model_cierre_cr = new CierreEtapaModel();
                $rta_cierre_cr = CierreEtapaResource::make($model_cierre_cr->create($datosCierreEtapa));
                $array_cierre_cr = json_decode(json_encode($rta_cierre_cr));

                //REGISTRAR EN EL LOG PROCESO DISCIPLINARIO
                $this->storeLogMigracion($cerrar_etapa_captura_reparto[0]->id_etapa, $array_proceso->id, $this->getDependenciaAmbientePruebas($proceso[0]->id_dependencia), Constants::FASE['cierre_captura_reparto'], $array_cierre_cr->id, auth()->user()->name, $datosProcesoDisciplinario['created_at']);

                // ACTUALIZAR ETAPA DEL PROCESO DISICIPLINARIO
                ClasificacionRadicadoModel::where('uuid', $datosCierreEtapa['id_proceso_disciplinario'])->update(['id_etapa' => Constants::ETAPA['evaluacion']]);

                //REGISTRAR EVALUACION
                $evaluacion = DB::select("SELECT
                            tipo_conducta,
                            noticia_priorizada,
                            resultado_evaluacion,
                            observaciones,
                            estado,
                            origen_registro
                            FROM DISC_CONDUCTA_TIPO_QUEJA
                            WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud . "
                            AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia . " AND origen_registro = 'J'");

                if (count($evaluacion) > 0 && ($datosClasificacion['id_tipo_expediente'] != Constants::TIPO_EXPEDIENTE['derecho_peticion'] || $datosClasificacion['id_tipo_expediente'] != Constants::TIPO_EXPEDIENTE['tutela'])) {

                    if ($evaluacion[0]->resultado_evaluacion == 1) {
                        $datosEvaluacion['resultado_evaluacion'] = Constants::RESULTADO_EVALUACION['comisorio_eje'];
                    } else if ($evaluacion[0]->resultado_evaluacion == 2) {
                        $datosEvaluacion['resultado_evaluacion'] = Constants::RESULTADO_EVALUACION['devolucion_entidad'];
                    } else if ($evaluacion[0]->resultado_evaluacion == 3) {
                        $datosEvaluacion['resultado_evaluacion'] = Constants::RESULTADO_EVALUACION['incorporacion'];
                    } else if ($evaluacion[0]->resultado_evaluacion == 5 || $evaluacion[0]->resultado_evaluacion == 9) {
                        $datosEvaluacion['resultado_evaluacion'] = Constants::RESULTADO_EVALUACION['remisorio_externo'];
                    } else if ($evaluacion[0]->resultado_evaluacion == 6  || $evaluacion[0]->resultado_evaluacion == 10) {
                        $datosEvaluacion['resultado_evaluacion'] = Constants::RESULTADO_EVALUACION['remisorio_interno'];
                    }

                    $datosEvaluacion['id_proceso_disciplinario'] = $array_proceso->id;
                    $datosEvaluacion['noticia_priorizada'] =  $evaluacion[0]->noticia_priorizada == 'SI' ? 1 : 2;
                    $datosEvaluacion['justificacion'] =  $evaluacion[0]->observaciones;
                    $datosEvaluacion['estado'] = Constants::ESTADO_EVALUACION['aprobado_por_jefe'];
                    $datosEvaluacion['tipo_conducta'] = 1; //$evaluacion[0]->tipo_conducta;
                    $datosEvaluacion['estado_evaluacion'] = 1;
                    $datosEvaluacion['id_etapa'] =  Constants::ETAPA['evaluacion'];
                    $datosEvaluacion['eliminado'] = false;
                    $datosEvaluacion['created_user'] = auth()->user()->name;

                    $model_evaluacion = new EvaluacionModel();
                    $rta_evaluacion = EvaluacionResource::make($model_evaluacion->create($datosEvaluacion));
                    $array_evaluacion = json_decode(json_encode($rta_evaluacion));

                    //REGISTRAR EN EL LOG PROCESO DISCIPLINARIO
                    $this->storeLogMigracion(Constants::ETAPA['evaluacion'], $array_proceso->id, $this->getDependenciaAmbientePruebas($proceso[0]->id_dependencia), Constants::FASE['evaluacion'], $array_evaluacion->id, auth()->user()->name, $datosProcesoDisciplinario['created_at']);
                }

                // REGISTRAR REMISION QUEJA
                $remision_queja = DB::select("SELECT
                            registrado_en,
                            asignado_dependencia,
                            fecha_asignacion
                            FROM DISC_QUEJA_A_REPARTO
                            WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud . "
                            AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia);

                if (count($remision_queja) > 0 && ($datosClasificacion['id_tipo_expediente'] != Constants::TIPO_EXPEDIENTE['derecho_peticion'] || $datosClasificacion['id_tipo_expediente'] != Constants::TIPO_EXPEDIENTE['tutela'])) {


                    $requestRemisionQueja['id_proceso_disciplinario'] = $array_proceso->id;
                    $requestRemisionQueja['id_tipo_evaluacion'] = $datosEvaluacion['resultado_evaluacion'];

                    $requestRemisionQueja['id_dependencia_origen'] = $this->getDependenciaAmbientePruebas($remision_queja[0]->registrado_en);
                    $requestRemisionQueja['id_dependencia_destino'] = $this->getDependenciaAmbientePruebas($remision_queja[0]->asignado_dependencia);
                    $requestRemisionQueja['eliminado'] = false;

                    $model = new RemisionQuejaModel();
                    $rta_remision_queja = RemisionQuejaResource::make($model->create($requestRemisionQueja));
                    $array_remision_queja = json_decode(json_encode($rta_remision_queja));

                    // REGISTRA LA INFORMACIÓN EN EL LOG
                    $this->storeLogMigracion(Constants::ETAPA['evaluacion'], $array_proceso->id, $this->getDependenciaAmbientePruebas($proceso[0]->id_dependencia), Constants::FASE['remision_queja'], $array_remision_queja->id, auth()->user()->name, $datosProcesoDisciplinario['created_at']);
                }


                // POR DEFECTO SE DEBE DILIGENCIAR GESTOR RESPUESTA
                $requestGestorRespuesta['id_proceso_disciplinario'] = $array_proceso->id;
                $requestGestorRespuesta['aprobado'] = 1;
                $requestGestorRespuesta['version'] = 1;
                $requestGestorRespuesta['nuevo_documento'] = 1;
                $requestGestorRespuesta['descripcion'] = "Proceso proveniente de migracion";
                $requestGestorRespuesta['proceso_finalizado'] = 1;
                $requestGestorRespuesta['created_user'] = "ForsecurityDiscUno";
                $requestGestorRespuesta['updated_user'] = "ForsecurityDiscUno";
                $requestGestorRespuesta['orden_funcionario'] = 1;
                $requestGestorRespuesta['id_mas_orden_funcionario'] = 1;
                $requestGestorRespuesta['id_documento_sirius'] = "no-aplica";
                $requestGestorRespuesta['eliminado'] = false;

                $model = new GestorRespuestaModel();
                GestorRespuestaResource::make($model->create($requestGestorRespuesta));

                // POR DEFECTO SE DEBE DILIGENCIAR COMUNICACIÓN INTERESADO
                $requestComunicacionInteresado['id_proceso_disciplinario'] = $array_proceso->id;
                $requestComunicacionInteresado['id_interesado'] = "no-aplica";
                $requestComunicacionInteresado['id_documento_sirius'] = "no-aplica";
                $requestComunicacionInteresado['estado'] = 1;
                $requestComunicacionInteresado['created_user'] = "ForsecurityDiscUno";
                $requestComunicacionInteresado['updated_user'] = "ForsecurityDiscUno";
                $requestComunicacionInteresado['eliminado'] = false;

                $model = new ComunicacionInteresadoModel();
                ComunicacionInteresadoResource::make($model->create($requestComunicacionInteresado));

                // POR DEFECTO SE DEBE DILIGENCIAR DOCUMENTO CIERRE
                $requestDocumentoCierre['id_proceso_disciplinario'] = $array_proceso->id;
                $requestDocumentoCierre['estado'] = 1;
                $requestDocumentoCierre['created_user'] = "ForsecurityDiscUno";
                $requestDocumentoCierre['updated_user'] = "ForsecurityDiscUno";
                $requestDocumentoCierre['seguimiento'] = null;
                $requestDocumentoCierre['descripcion_seguimiento'] = null;
                $requestDocumentoCierre['eliminado'] = false;

                $model = new DocumentoCierreModel();
                DocumentoCierreResource::make($model->create($requestDocumentoCierre));

                // POR DEFECTO SE DEBE DILIGENCIAR INFORME CIERRE
                $requestInformeDocumentoCierre['id_proceso_disciplinario'] = $array_proceso->id;
                $requestInformeDocumentoCierre['finalizado'] = 1;
                $requestInformeDocumentoCierre['descripcion'] = "no-aplica";
                /*$requestInformeDocumentoCierre['id_etapa'] = 2;
                        $requestInformeDocumentoCierre['id_fase'] = 18;
                        $requestInformeDocumentoCierre['id_documento_sirius'] = "no-aplica";
                        $requestInformeDocumentoCierre['id_dependencia'] = "";
                        $requestInformeDocumentoCierre['created_user'] = "ForsecurityDiscUno";
                        $requestInformeDocumentoCierre['eliminado'] = false;
                        $requestInformeDocumentoCierre['documento_sirius'] = null;
                        $requestInformeDocumentoCierre['radicado_sirius'] = null;*/

                $model = new InformeCierreModel();
                InformeCierreResource::make($model->create($requestInformeDocumentoCierre));




                //CERRAR ETAPA EVALUACIÓN
                $cerrar_etapa_evaluacion = DB::select("SELECT
                            id_etapa,
                            fecha_cierre,
                            dependencia_finaliza,
                            detalle_cierre
                            FROM DISC_CIERRE_ETAPAS
                            WHERE num_solicitud = " . $query_disc_solicitudes[$cont]->num_solicitud . "
                            AND vigencia = " . $query_disc_solicitudes[$cont]->vigencia . " AND id_etapa = 2");

                if (count($cerrar_etapa_evaluacion) > 0) {

                    $datosCierreEtapa['id_etapa'] =  Constants::ETAPA['evaluacion'];
                    $datosCierreEtapa['id_proceso_disciplinario'] = $array_proceso->id;
                    $datosCierreEtapa['created_user'] = auth()->user()->name;
                    $datosCierreEtapa['id_funcionario_asignado'] = auth()->user()->name;
                    $datosCierreEtapa['eliminado'] = false;
                    $datosCierreEtapa['created_at'] = $cerrar_etapa_evaluacion[0]->fecha_cierre;

                    $model_cierre_ev = new CierreEtapaModel();
                    $rta_cierre_ev = CierreEtapaResource::make($model_cierre_ev->create($datosCierreEtapa));
                    $array_cierre_ev = json_decode(json_encode($rta_cierre_ev));

                    if ($datosClasificacion['id_tipo_expediente'] == Constants::TIPO_EXPEDIENTE['proceso_disciplinario'] || $datosClasificacion['id_tipo_expediente'] == Constants::TIPO_EXPEDIENTE['queja']) {

                        if (count($evaluacion) > 0 && count($remision_queja) == 0) {
                            $dependencia_asignada = auth()->user()->id_dependencia;
                            $estado = Constants::ESTADO_PROCESO_DISCIPLINARIO['activo'];
                            $id_etapa = Constants::ETAPA['evaluacion_pd'];
                        } else if (count($evaluacion) > 0 && count($remision_queja) > 0) {
                            $this->reclasificacionProcesoDisciplinario($array_proceso->id);
                            $remision_queja[0]->asignado_dependencia = auth()->user()->id_dependencia; // SOLO DEBE USARSE EN AMBIENTE DE PRUEBAS
                            $dependencia_asignada = $remision_queja[0]->asignado_dependencia;
                            $estado = Constants::ESTADO_PROCESO_DISCIPLINARIO['activo'];
                            $id_etapa = Constants::ETAPA['evaluacion_pd'];
                        }
                    } else {
                        $dependencia_asignada = auth()->user()->id_dependencia;
                        $estado = Constants::ESTADO_PROCESO_DISCIPLINARIO['cerrado'];
                        $id_etapa = Constants::ETAPA['evaluacion'];
                    }


                    // SE ACTUALIZA ETAPA DEL PROCESO Y DEPENDENCIA ACTUAL
                    ProcesoDiciplinarioModel::where('UUID', $array_proceso->id)
                        ->update([
                            'id_dependencia_actual' => $dependencia_asignada,
                            'id_dependencia_duena' => $dependencia_asignada,
                            'id_etapa' => $id_etapa,
                            'estado' => $estado
                        ]);

                    // REGISTRA LA INFORMACIÓN EN EL LOG
                    $this->storeLogMigracion($id_etapa, $array_proceso->id, $dependencia_asignada, Constants::FASE['cierre_evaluacion'], $array_cierre_ev->id, auth()->user()->name, $datosProcesoDisciplinario['created_at']);
                }

                DB::connection()->commit();
            } else if (count($etapa) == 0 && count($existe_proceso) == 0) {
            }
        }
    }
}
