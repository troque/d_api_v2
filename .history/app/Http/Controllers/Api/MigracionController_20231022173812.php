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
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class MigracionController extends Controller
{
    use MigracionesTrait;
    use LogTrait;
    use ReclasificacionTrait;

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getInfoProcesoDisciplinario($expediente, $vigencia)
    {
        // CONSULTAR EN MIGRACION
        $data_migracion['fechaRegistroDesde'] = null;
        $data_migracion['fechaRegistroHasta'] = null;
        $data_migracion['version'] = null;
        $data_migracion['vigencia'] = "";
        $data_migracion['numeroRadicado'] = $expediente;
        $data_migracion['nombreResponsable'] = "";
        $data_migracion['idResponsable'] = "";
        $data_migracion['dependencia'] = "";
        $data_migracion['idDependencia'] = "";
        $data_migracion['tipoInteresado'] = "";
        $rta_migracion = $this->buscarExpedientePorNumeroRadicado($data_migracion);

        $respuesta = $this->buscarDetalleExpediente($expediente, $vigencia);

        // SE VALIDA SI EL PROCESO DISCIPLINARIO NO ESTA REGISTRADO EN MIGRACION
        $count_antecedentes = count(json_decode(json_encode($respuesta['objectresponse']['antecedentes'])));
        $count_interesados = count(json_decode(json_encode($respuesta['objectresponse']['interesados'])));
        $count_entidades = count(json_decode(json_encode($respuesta['objectresponse']['entidadesInvestigados'])));
        $count_actuaciones = count(json_decode(json_encode($respuesta['objectresponse']['actuaciones'])));

        if ($count_antecedentes == 0 && $count_interesados == 0 && $count_entidades == 0 && $count_actuaciones == 0) {

            $error['estado'] = false;
            $error['error'] = 'Este proceso disciplinario no se encuentra registrado en el sistema de migración.';
            return json_encode($error);
        }

        $array = array(); //creamos un array

        $reciboDatos['attributes']['radicado'] =  $respuesta['objectresponse']['numRadicado'];
        $reciboDatos['attributes']['vigencia'] = $respuesta['objectresponse']['vigencia'];
        $reciboDatos['attributes']['ubicacion_expediente'] = $respuesta['objectresponse']['dependenciaActual']['nombre'];
        $reciboDatos['attributes']['etapa'] =  $respuesta['objectresponse']['etapaActual'];
        $reciboDatos['attributes']['antecedente'] =  $respuesta['objectresponse']['antecedentes'] == null ? null : $respuesta['objectresponse']['antecedentes'][0]['hechos'];
        $reciboDatos['attributes']['estado'] =  $respuesta['objectresponse']['estadoActual'];
        $reciboDatos['attributes']['tipoExpediente'] =  $respuesta['objectresponse']['tipoExpediente'];
        $reciboDatos['attributes']['evaluacion'] =  $respuesta['objectresponse']['evaluacion'];
        $reciboDatos['attributes']['radicado_padre_desglose'] =  $respuesta['objectresponse']['padreDesglose'];
        $reciboDatos['attributes']['vigencia_padre_desglose'] =  "";
        $reciboDatos['attributes']['responsable_actual'] =  $respuesta['objectresponse']['responsableActual']['nombreCompleto'];

        //VALIDAR SI EL PROCESO YA FUE MIGRADO.
        $validar_proceso_disciplinario = DB::select("select radicado from proceso_disciplinario where radicado = '" . $expediente . "' and vigencia = " . $vigencia);

        if (!empty($validar_proceso_disciplinario)) {
            $reciboDatos['attributes']['migrado'] =  true;
        } else {
            $reciboDatos['attributes']['migrado'] =  false;
        }

        for ($cont = 0; $cont < count($rta_migracion['objectresponse']); $cont++) {
            if ($rta_migracion['objectresponse'][$cont]['numeroRadicado'] == $expediente  && $rta_migracion['objectresponse'][$cont]['vigencia'] == $vigencia) {
                $reciboDatos['attributes']['dependenciaOrigen'] =  $rta_migracion['objectresponse'][$cont]['dependencia'];
                $reciboDatos['attributes']['idDependenciaOrigen'] =  $rta_migracion['objectresponse'][$cont]['idDependencia'];
                $reciboDatos['attributes']['registradoPor'] =  $rta_migracion['objectresponse'][$cont]['nombreResponsable'];
                $reciboDatos['attributes']['fechaRegistro'] =  $rta_migracion['objectresponse'][$cont]['fechaRegistro'];

                $cont = count($rta_migracion['objectresponse']);
            }
        }

        array_push($array, $reciboDatos);

        $json['data'] = $array;
        return json_encode($json);
    }

    /**
     *
     */
    public function getInfoListaAntecedentes($expediente, $vigencia)
    {

        // VALIDA QUE PREVIAMENTE EXISTA UN REGISTRO EN LA TABLA TEMP_PROCESO_DISCIPLINARIO PARA MOSTRAR LA LISTA DE ANTECEDENTES
        $validacion_proceso = DB::select("
            select
                radicado
            from
                temp_proceso_disciplinario
            where radicado = '" . $expediente . "'
            and vigencia = " . $vigencia);

        if (count($validacion_proceso) == 0) {

            $error['estado'] = false;
            $error['error'] = 'Para consultar la información debe primero completar la fase Inicio proceso disciplinario';
            return json_encode($error);
        }


        $respuesta = $this->buscarDetalleExpediente($expediente, $vigencia);

        $array = array(); //creamos un array

        for ($cont = 0; $cont < count($respuesta['objectresponse']['antecedentes']); $cont++) {

            $reciboDatos['attributes']['id'] = $cont;
            $reciboDatos['attributes']['version'] =  $respuesta['objectresponse']['antecedentes'][$cont]['version'];
            $reciboDatos['attributes']['antecedente'] =  $respuesta['objectresponse']['antecedentes'][$cont]['hechos'];
            $reciboDatos['attributes']['fecha'] =  $respuesta['objectresponse']['antecedentes'][$cont]['fechaRegistro'];
            $reciboDatos['attributes']['dependenciaRegistro'] =  $respuesta['objectresponse']['antecedentes'][$cont]['dependenciaRegistro'];
            $reciboDatos['attributes']['registradoPor'] =  $respuesta['objectresponse']['antecedentes'][$cont]['registradoPor'];

            $query = DB::select("select item from temp_antecedentes where item = " . $cont . " and radicado = '" . $expediente . "' and vigencia = " . $vigencia);

            if (count($query) > 0) {
                $reciboDatos['attributes']['semaforizacion'] =  Constants::SEMAFORIZACION['green'];
            } else {
                $reciboDatos['attributes']['semaforizacion'] =  Constants::SEMAFORIZACION['red'];
            }

            array_push($array, $reciboDatos);
        }

        $json['data'] = $array;

        return json_encode($json);
    }


    /**
     *
     */
    public function getInfoAntecedente($expediente, $vigencia, $id_antecedente)
    {

        $respuesta = $this->buscarDetalleExpediente($expediente, $vigencia);

        $array = array(); //creamos un array

        if (count($respuesta['objectresponse']['antecedentes']) > 0) {

            $reciboDatos['attributes']['id'] = $id_antecedente;
            $reciboDatos['attributes']['version'] =  $respuesta['objectresponse']['antecedentes'][$id_antecedente]['version'];
            $reciboDatos['attributes']['antecedente'] =  $respuesta['objectresponse']['antecedentes'][$id_antecedente]['hechos'];
            $reciboDatos['attributes']['fecha'] =  $respuesta['objectresponse']['antecedentes'][$id_antecedente]['fechaRegistro'];
            $reciboDatos['attributes']['dependenciaRegistro'] =  $respuesta['objectresponse']['antecedentes'][$id_antecedente]['dependenciaRegistro'];
            $reciboDatos['attributes']['registradoPor'] =  $respuesta['objectresponse']['antecedentes'][$id_antecedente]['registradoPor'];
        } else {

            $reciboDatos['attributes']['id'] = $id_antecedente;
            $reciboDatos['attributes']['version'] =  "";
            $reciboDatos['attributes']['antecedente'] =  "";
            $reciboDatos['attributes']['fecha'] =  "";
            $reciboDatos['attributes']['dependenciaRegistro'] =  "";
            $reciboDatos['attributes']['registradoPor'] = "";
        }

        //VALIDAR SI EL PROCESO YA FUE MIGRADO.
        $validar_proceso_disciplinario = DB::select("select radicado from proceso_disciplinario where radicado = '" . $expediente . "' and vigencia = " . $vigencia);

        if (!empty($validar_proceso_disciplinario)) {
            $reciboDatos['attributes']['migrado'] =  true;
        } else {
            $reciboDatos['attributes']['migrado'] =  false;
        }


        array_push($array, $reciboDatos);

        $json['data'] = $array;
        return json_encode($json);
    }


    /**
     *
     */
    public function getInfoListaInteresados($expediente, $vigencia)
    {

        // VALIDA QUE PREVIAMENTE EXISTA UN REGISTRO EN LA TABLA TEMP_PROCESO_DISCIPLINARIO PARA MOSTRAR LA LISTA DE INTERESADOS
        $validacion_proceso = DB::select("
            select
                radicado
            from
                temp_proceso_disciplinario
            where radicado = '" . $expediente . "'
            and vigencia = " . $vigencia);

        if (count($validacion_proceso) == 0) {

            $error['estado'] = false;
            $error['error'] = 'Para consultar la información debe primero completar la fase Inicio proceso disciplinario';
            return json_encode($error);
        }

        $respuesta = $this->buscarDetalleExpediente($expediente, $vigencia);

        $array = array(); //creamos un array

        for ($cont = 0; $cont < count($respuesta['objectresponse']['interesados']); $cont++) {

            $reciboDatos['attributes']['id'] = $cont;
            $reciboDatos['attributes']['version'] = $respuesta['objectresponse']['interesados'][$cont]['version'];
            $reciboDatos['attributes']['numeroDocumento'] = $respuesta['objectresponse']['interesados'][$cont]['numeroDocumento'];
            $reciboDatos['attributes']['primerNombre'] = $respuesta['objectresponse']['interesados'][$cont]['primerNombre'];
            $reciboDatos['attributes']['segundoNombre'] = $respuesta['objectresponse']['interesados'][$cont]['segundoNombre'];
            $reciboDatos['attributes']['primerApellido'] = $respuesta['objectresponse']['interesados'][$cont]['primerApellido'];
            $reciboDatos['attributes']['segundoApellido'] = $respuesta['objectresponse']['interesados'][$cont]['segundoApellido'];
            $reciboDatos['attributes']['nombreCompleto'] = $respuesta['objectresponse']['interesados'][$cont]['nombreCompleto'];
            $reciboDatos['attributes']['email'] = $respuesta['objectresponse']['interesados'][$cont]['email'];
            $reciboDatos['attributes']['telefono'] = $respuesta['objectresponse']['interesados'][$cont]['telefono'];
            $reciboDatos['attributes']['telefono2'] = $respuesta['objectresponse']['interesados'][$cont]['telefono2'];
            $reciboDatos['attributes']['lugarNacimiento'] = $respuesta['objectresponse']['interesados'][$cont]['lugarNacimiento'];
            $reciboDatos['attributes']['profesion'] = $respuesta['objectresponse']['interesados'][$cont]['profesion'];
            $reciboDatos['attributes']['idOrientacionSexual'] = $respuesta['objectresponse']['interesados'][$cont]['idOrientacionSexual'];
            $reciboDatos['attributes']['idSexo'] = $respuesta['objectresponse']['interesados'][$cont]['idSexo'];
            $reciboDatos['attributes']['idNacionalidad'] = $respuesta['objectresponse']['interesados'][$cont]['idNacionalidad'];
            $reciboDatos['attributes']['codMunicipio'] = $respuesta['objectresponse']['interesados'][$cont]['codMunicipio'];
            $reciboDatos['attributes']['localidad'] = $respuesta['objectresponse']['interesados'][$cont]['localidad'];
            $reciboDatos['attributes']['ciudad'] = $respuesta['objectresponse']['interesados'][$cont]['ciudad'];
            $reciboDatos['attributes']['barrio'] = $respuesta['objectresponse']['interesados'][$cont]['barrio'];
            $reciboDatos['attributes']['direccion'] = $respuesta['objectresponse']['interesados'][$cont]['direccion'];
            $reciboDatos['attributes']['diligenciadoPor'] = $respuesta['objectresponse']['interesados'][$cont]['diligenciadoPor'];
            $reciboDatos['attributes']['idTipoEntidad'] = $respuesta['objectresponse']['interesados'][$cont]['idTipoEntidad'];
            $reciboDatos['attributes']['nombreEntidad'] = $respuesta['objectresponse']['interesados'][$cont]['nombreEntidad'];
            $reciboDatos['attributes']['dependenciaEntidad'] = $respuesta['objectresponse']['interesados'][$cont]['dependenciaEntidad'];
            $reciboDatos['attributes']['sectorEntidad'] = $respuesta['objectresponse']['interesados'][$cont]['sectorEntidad'];
            $reciboDatos['attributes']['tipoSujetoProcesal'] = $respuesta['objectresponse']['interesados'][$cont]['tipoInteresado'];

            $query = DB::select("select item from temp_interesados where item = " . $cont . " and radicado = '" . $expediente . "' and vigencia = " . $vigencia);

            if (count($query) > 0) {
                $reciboDatos['attributes']['semaforizacion'] =  Constants::SEMAFORIZACION['green'];
            } else {
                $reciboDatos['attributes']['semaforizacion'] =  Constants::SEMAFORIZACION['red'];
            }

            array_push($array, $reciboDatos);
        }


        $json['data'] = $array;
        return json_encode($json);
    }

    /**
     *
     */
    public function getInfoInteresado($expediente, $vigencia, $id_interesado)
    {

        $respuesta = $this->buscarDetalleExpediente($expediente, $vigencia);

        error_log("Valor de respuesta: " . count($respuesta['objectresponse']['interesados']));
        $array = array(); //creamos un array

        if (count($respuesta['objectresponse']['interesados']) > 0) {

            $reciboDatos['attributes']['id'] = $id_interesado;
            $reciboDatos['attributes']['version'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['version'];
            $reciboDatos['attributes']['numeroDocumento'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['numeroDocumento'];
            $reciboDatos['attributes']['primerNombre'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['primerNombre'];
            $reciboDatos['attributes']['segundoNombre'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['segundoNombre'];
            $reciboDatos['attributes']['primerApellido'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['primerApellido'];
            $reciboDatos['attributes']['segundoApellido'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['segundoApellido'];
            $reciboDatos['attributes']['nombreCompleto'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['nombreCompleto'];
            $reciboDatos['attributes']['email'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['email'];
            $reciboDatos['attributes']['telefono'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['telefono'];
            $reciboDatos['attributes']['telefono2'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['telefono2'];
            $reciboDatos['attributes']['lugarNacimiento'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['lugarNacimiento'];
            $reciboDatos['attributes']['profesion'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['profesion'];
            $reciboDatos['attributes']['idOrientacionSexual'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['idOrientacionSexual'];
            $reciboDatos['attributes']['idSexo'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['idSexo'];
            $reciboDatos['attributes']['idNacionalidad'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['idNacionalidad'];
            $reciboDatos['attributes']['codMunicipio'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['codMunicipio'];
            $reciboDatos['attributes']['localidad'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['localidad'];
            $reciboDatos['attributes']['ciudad'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['ciudad'];
            $reciboDatos['attributes']['barrio'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['barrio'];
            $reciboDatos['attributes']['direccion'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['direccion'];
            $reciboDatos['attributes']['diligenciadoPor'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['diligenciadoPor'];
            $reciboDatos['attributes']['idTipoEntidad'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['idTipoEntidad'];
            $reciboDatos['attributes']['nombreEntidad'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['nombreEntidad'];
            $reciboDatos['attributes']['dependenciaEntidad'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['dependenciaEntidad'];
            $reciboDatos['attributes']['sectorEntidad'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['sectorEntidad'];
            $reciboDatos['attributes']['tipoSujetoProcesal'] =  $respuesta['objectresponse']['interesados'][$id_interesado]['tipoInteresado'];
        } else {

            $reciboDatos['attributes']['id'] = $id_interesado;
            $reciboDatos['attributes']['version'] =  "";
            $reciboDatos['attributes']['numeroDocumento'] =  "";
            $reciboDatos['attributes']['primerNombre'] =  "";
            $reciboDatos['attributes']['segundoNombre'] =  "";
            $reciboDatos['attributes']['primerApellido'] =  "";
            $reciboDatos['attributes']['segundoApellido'] =  "";
            $reciboDatos['attributes']['nombreCompleto'] =  "";
            $reciboDatos['attributes']['email'] =  "";
            $reciboDatos['attributes']['telefono'] =  "";
            $reciboDatos['attributes']['telefono2'] =  "";
            $reciboDatos['attributes']['lugarNacimiento'] =  "";
            $reciboDatos['attributes']['profesion'] =  "";
            $reciboDatos['attributes']['idOrientacionSexual'] =  "";
            $reciboDatos['attributes']['idSexo'] =  "";
            $reciboDatos['attributes']['idNacionalidad'] =  "";
            $reciboDatos['attributes']['codMunicipio'] =  "";
            $reciboDatos['attributes']['localidad'] =  "";
            $reciboDatos['attributes']['ciudad'] =  "";
            $reciboDatos['attributes']['barrio'] =  "";
            $reciboDatos['attributes']['direccion'] =  "";
            $reciboDatos['attributes']['diligenciadoPor'] =  "";
            $reciboDatos['attributes']['idTipoEntidad'] =  "";
            $reciboDatos['attributes']['nombreEntidad'] =  "";
            $reciboDatos['attributes']['dependenciaEntidad'] =  "";
            $reciboDatos['attributes']['sectorEntidad'] =  "";
            $reciboDatos['attributes']['tipoSujetoProcesal'] =  "";
        }

        //VALIDAR SI EL PROCESO YA FUE MIGRADO.
        $validar_proceso_disciplinario = DB::select("select radicado from proceso_disciplinario where radicado = '" . $expediente . "' and vigencia = " . $vigencia);

        if (!empty($validar_proceso_disciplinario)) {
            $reciboDatos['attributes']['migrado'] =  true;
        } else {
            $reciboDatos['attributes']['migrado'] =  false;
        }

        array_push($array, $reciboDatos);
        $json['data'] = $array;
        return json_encode($json);
    }

    /**
     *
     */
    public function getListaProcesosDisciplinario($radicado)
    {

        $respuesta = $this->buscarExpedientePorNumeroRadicado($radicado);
        $array = array(); //creamos un array

        $json['data'] = $array;
        return json_encode($json);
    }

    /**
     *
     */
    public function getInfoListaEntidades($expediente, $vigencia)
    {

        // VALIDA QUE PREVIAMENTE EXISTA UN REGISTRO EN LA TABLA TEMP_PROCESO_DISCIPLINARIO PARA MOSTRAR LA LISTA DE ENTIDADES
        $validacion_proceso = DB::select("
            select
                radicado
            from
                temp_proceso_disciplinario
            where radicado = '" . $expediente . "'
            and vigencia = " . $vigencia);

        if (count($validacion_proceso) == 0) {

            $error['estado'] = false;
            $error['error'] = 'Para consultar la información debe primero completar la fase Inicio proceso disciplinario';
            return json_encode($error);
        }


        $respuesta = $this->buscarDetalleExpediente($expediente, $vigencia);

        $array = array();

        for ($cont = 0; $cont < count($respuesta['objectresponse']['entidadesInvestigados']); $cont++) {

            $reciboDatos['attributes']['id'] = $cont;
            $reciboDatos['attributes']['version'] = $respuesta['objectresponse']['entidadesInvestigados'][$cont]['version'];
            $reciboDatos['attributes']['idEntidad'] = $respuesta['objectresponse']['entidadesInvestigados'][$cont]['idEntidad'];
            $reciboDatos['attributes']['fechaRegistro'] = $respuesta['objectresponse']['entidadesInvestigados'][$cont]['fechaRegistro'];
            $reciboDatos['attributes']['entidad'] = $respuesta['objectresponse']['entidadesInvestigados'][$cont]['entidad'];
            $reciboDatos['attributes']['direccion'] = $respuesta['objectresponse']['entidadesInvestigados'][$cont]['direccion'];
            $reciboDatos['attributes']['sector'] = $respuesta['objectresponse']['entidadesInvestigados'][$cont]['sector'];
            $reciboDatos['attributes']['nombreInvestigado'] = $respuesta['objectresponse']['entidadesInvestigados'][$cont]['nombreInvestigado'];
            $reciboDatos['attributes']['cargoInvestigado'] = $respuesta['objectresponse']['entidadesInvestigados'][$cont]['cargoInvestigado'];
            $reciboDatos['attributes']['observaciones'] = $respuesta['objectresponse']['entidadesInvestigados'][$cont]['observaciones'];
            $reciboDatos['attributes']['registradoPor'] = $respuesta['objectresponse']['entidadesInvestigados'][$cont]['registradoPor'];
            $reciboDatos['attributes']['dependenciaRegistro'] = $respuesta['objectresponse']['entidadesInvestigados'][$cont]['dependenciaRegistro'];

            $query = DB::select("select item from temp_entidades where item = " . $cont . " and radicado = '" . $expediente . "' and vigencia = " . $vigencia);

            if (count($query) > 0) {
                $reciboDatos['attributes']['semaforizacion'] =  Constants::SEMAFORIZACION['green'];
            } else {
                $reciboDatos['attributes']['semaforizacion'] =  Constants::SEMAFORIZACION['red'];
            }

            array_push($array, $reciboDatos);
        }

        $json['data'] = $array;
        return json_encode($json);
    }

    /**
     *
     */
    public function getInfoEntidad($expediente, $vigencia, $id_entidad)
    {

        $respuesta = $this->buscarDetalleExpediente($expediente, $vigencia);

        $array = array(); //creamos un array

        if (count($respuesta['objectresponse']['entidadesInvestigados']) > 0) {

            $reciboDatos['attributes']['id'] = $id_entidad;
            $reciboDatos['attributes']['version'] = $respuesta['objectresponse']['entidadesInvestigados'][$id_entidad]['version'];
            $reciboDatos['attributes']['idEntidad'] = $respuesta['objectresponse']['entidadesInvestigados'][$id_entidad]['idEntidad'];
            $reciboDatos['attributes']['fechaRegistro'] = $respuesta['objectresponse']['entidadesInvestigados'][$id_entidad]['fechaRegistro'];
            $reciboDatos['attributes']['entidad'] = $respuesta['objectresponse']['entidadesInvestigados'][$id_entidad]['entidad'];
            $reciboDatos['attributes']['direccion'] = $respuesta['objectresponse']['entidadesInvestigados'][$id_entidad]['direccion'];
            $reciboDatos['attributes']['sector'] = $respuesta['objectresponse']['entidadesInvestigados'][$id_entidad]['sector'];
            $reciboDatos['attributes']['nombreInvestigado'] = $respuesta['objectresponse']['entidadesInvestigados'][$id_entidad]['nombreInvestigado'];
            $reciboDatos['attributes']['cargoInvestigado'] = $respuesta['objectresponse']['entidadesInvestigados'][$id_entidad]['cargoInvestigado'];
            $reciboDatos['attributes']['observaciones'] = $respuesta['objectresponse']['entidadesInvestigados'][$id_entidad]['observaciones'];
            $reciboDatos['attributes']['registradoPor'] = $respuesta['objectresponse']['entidadesInvestigados'][$id_entidad]['registradoPor'];
            $reciboDatos['attributes']['dependenciaRegistro'] = $respuesta['objectresponse']['entidadesInvestigados'][$id_entidad]['dependenciaRegistro'];
        } else {

            $reciboDatos['attributes']['id'] = $id_entidad;
            $reciboDatos['attributes']['version'] = "";
            $reciboDatos['attributes']['idEntidad'] = "";
            $reciboDatos['attributes']['fechaRegistro'] = "";
            $reciboDatos['attributes']['entidad'] = "";
            $reciboDatos['attributes']['direccion'] = "";
            $reciboDatos['attributes']['sector'] = "";
            $reciboDatos['attributes']['nombreInvestigado'] = "";
            $reciboDatos['attributes']['cargoInvestigado'] = "";
            $reciboDatos['attributes']['observaciones'] =  "";
            $reciboDatos['attributes']['registradoPor'] =  "";
            $reciboDatos['attributes']['dependenciaRegistro'] =  "";
        }

        //VALIDAR SI EL PROCESO YA FUE MIGRADO.
        $validar_proceso_disciplinario = DB::select("select radicado from proceso_disciplinario where radicado = '" . $expediente . "' and vigencia = " . $vigencia);

        if (!empty($validar_proceso_disciplinario)) {
            $reciboDatos['attributes']['migrado'] =  true;
        } else {
            $reciboDatos['attributes']['migrado'] =  false;
        }

        array_push($array, $reciboDatos);

        $json['data'] = $array;
        return json_encode($json);
    }

    /**
     *
     */
    public function getInfoListaActuaciones($expediente, $vigencia)
    {

        // VALIDA QUE PREVIAMENTE EXISTA UN REGISTRO EN LA TABLA TEMP_PROCESO_DISCIPLINARIO PARA MOSTRAR LA LISTA DE ACTUACIONES
        $validacion_proceso = DB::select("
            select
                radicado
            from
                temp_proceso_disciplinario
            where radicado = '" . $expediente . "'
            and vigencia = " . $vigencia);

        if (count($validacion_proceso) == 0) {

            $error['estado'] = false;
            $error['error'] = 'Para consultar la información debe primero completar la fase Inicio proceso disciplinario';
            return json_encode($error);
        }


        $respuesta = $this->buscarDetalleExpediente($expediente, $vigencia);

        $array = array();

        for ($cont = 0; $cont < count($respuesta['objectresponse']['actuaciones']); $cont++) {

            $reciboDatos['attributes']['id'] = $cont;
            $reciboDatos['attributes']['version'] = $respuesta['objectresponse']['actuaciones'][$cont]['version'];
            $reciboDatos['attributes']['nombre'] = $respuesta['objectresponse']['actuaciones'][$cont]['nombre'];
            $reciboDatos['attributes']['tipo'] = $respuesta['objectresponse']['actuaciones'][$cont]['tipo'];
            $reciboDatos['attributes']['autoNumero'] = $respuesta['objectresponse']['actuaciones'][$cont]['autoNumero'];
            $reciboDatos['attributes']['fechaString'] = $respuesta['objectresponse']['actuaciones'][$cont]['fechaString'];
            $reciboDatos['attributes']['fecha'] = $respuesta['objectresponse']['actuaciones'][$cont]['fecha'];
            $reciboDatos['attributes']['fechaTerminoString'] = $respuesta['objectresponse']['actuaciones'][$cont]['fechaTerminoString'];
            $reciboDatos['attributes']['fechaTermino'] = $respuesta['objectresponse']['actuaciones'][$cont]['fechaTermino'];
            $reciboDatos['attributes']['instancia'] = $respuesta['objectresponse']['actuaciones'][$cont]['instancia'];
            $reciboDatos['attributes']['decision'] = $respuesta['objectresponse']['actuaciones'][$cont]['decision'];
            $reciboDatos['attributes']['terminoMonto'] = $respuesta['objectresponse']['actuaciones'][$cont]['terminoMonto'];
            $reciboDatos['attributes']['observacion'] = $respuesta['objectresponse']['actuaciones'][$cont]['observacion'];

            $query = DB::select("select item from temp_actuaciones where item = " . $cont . " and radicado = '" . $expediente . "' and vigencia = " . $vigencia);

            if (count($query) > 0) {
                $reciboDatos['attributes']['semaforizacion'] =  Constants::SEMAFORIZACION['green'];
            } else {
                $reciboDatos['attributes']['semaforizacion'] =  Constants::SEMAFORIZACION['red'];
            }

            array_push($array, $reciboDatos);
        }

        $json['data'] = $array;
        return json_encode($json);
    }

    /**
     *
     */
    public function getInfoActuacion($expediente, $vigencia, $id_actuacion)
    {

        $respuesta = $this->buscarDetalleExpediente($expediente, $vigencia);

        $array = array();

        $reciboDatos['attributes']['version'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['version'];
        $reciboDatos['attributes']['nombre'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['nombre'];
        $reciboDatos['attributes']['tipo'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['tipo'];
        $reciboDatos['attributes']['autoNumero'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['autoNumero'];
        $reciboDatos['attributes']['fechaString'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['fechaString'];
        $reciboDatos['attributes']['fecha'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['fecha'];
        $reciboDatos['attributes']['fechaTerminoString'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['fechaTerminoString'];
        $reciboDatos['attributes']['fechaTermino'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['fechaTermino'];
        $reciboDatos['attributes']['instancia'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['instancia'];
        $reciboDatos['attributes']['decision'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['decision'];
        $reciboDatos['attributes']['terminoMonto'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['terminoMonto'];
        $reciboDatos['attributes']['observacion'] = $respuesta['objectresponse']['actuaciones'][$id_actuacion]['observacion'];

        array_push($array, $reciboDatos);

        $json['data'] = $array;
        return json_encode($json);

        //VALIDAR SI EL PROCESO YA FUE MIGRADO.
        $validar_proceso_disciplinario = DB::select("select radicado from proceso_disciplinario where radicado = '" . $expediente . "' and vigencia = " . $vigencia);

        if (!empty($validar_proceso_disciplinario)) {
            $reciboDatos['attributes']['migrado'] =  true;
        } else {
            $reciboDatos['attributes']['migrado'] =  false;
        }
    }

    /**
     *
     */
    public function MigrarProcesoDefinitivo($radicado, $vigencia)
    {
        try {

            //DB::connection()->beginTransaction();

            //VALIDAR SI EL PROCESO YA FUE MIGRADO.
            $validar_proceso_disciplinario = DB::select("select radicado from proceso_disciplinario
                    where radicado = '" . $radicado . "' and vigencia = " . $vigencia);

            $id_clasificacion = null;

            if (!empty($validar_proceso_disciplinario)) {

                $error['estado'] = false;
                $error['error'] = 'Se identifica que el proceso con número de radicado ' . $radicado . ' - ' . $vigencia . ' ya fue migrado.';
                return json_encode($error);
            }


            $proceso_disciplinario = DB::select("select radicado, vigencia, estado, id_tipo_proceso, id_etapa,
                    id_dependencia_origen, id_dependencia_duena, created_user, created_at, id_tipo_evaluacion,
                    id_tipo_expediente, id_sub_tipo_expediente,
                    id_tipo_conducta, radicado_padre_desglose, vigencia_padre_desglose, usuario_actual from temp_proceso_disciplinario
                    where radicado = '" . $radicado . "' and vigencia = " . $vigencia);

            if (!empty($proceso_disciplinario)) {

                $antecedente = DB::select("select descripcion, fecha_registro from temp_antecedentes where radicado = '" . $radicado . "' and vigencia = " . $vigencia);

                $datos_interesado = DB::select("select
                        tipo_interesado, tipo_sujeto_procesal, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido,
                        tipo_documento, numero_documento, email, telefono, telefono2, cargo, orientacion_sexual, sexo, direccion,
                        departamento, ciudad, localidad, entidad, sector
                        from temp_interesados where radicado = '" . $radicado . "' and vigencia = " . $vigencia);

                $entidad = DB::select("select
                        sector, direccion, nombre_investigado, cargo_investigado, observaciones,
                        id_entidad from temp_entidades where radicado = '" . $radicado . "' and vigencia = " . $vigencia);

                $actuaciones = DB::select("select nombre, tipo, autonumero, fecha, path from temp_actuaciones where radicado = '" . $radicado . "' and vigencia = " . $vigencia);

                $etapa =  DB::select("select nombre from mas_etapa where id = " . $proceso_disciplinario[0]->id_etapa);

                $usuario_actual =  DB::select("select name from users where id = " . $proceso_disciplinario[0]->usuario_actual);

                if ($proceso_disciplinario[0]->id_etapa == Constants::ETAPA['captura_reparto'] && empty($antecedente)) {

                    $error['estado'] = false;
                    $error['error'] = 'Se identifica que el proceso se encuentra en la etapa ' . $etapa[0]->nombre . ', por lo tanto es necesario completar las fases: Inicio de proceso y antecedentes.';
                    return json_encode($error);
                } else if ($proceso_disciplinario[0]->id_etapa > Constants::ETAPA['captura_reparto'] && (empty($antecedente) || empty($datos_interesado) || empty($entidad))) {

                    $error['estado'] = false;
                    $error['error'] = 'Se identifica que el proceso se encuentra en la etapa ' . $etapa[0]->nombre . ', por lo tanto es necesario completar las fases: Inicio de proceso, antecedentes, datos del interesado e entidad del investigado.';
                    return json_encode($error);
                }
            } else {
                $error['estado'] = false;
                $error['error'] = 'La fase inicio proceso disciplinario es obligatoria.';
                return json_encode($error);
            }


            if (!empty($proceso_disciplinario) && $proceso_disciplinario[0]->id_etapa >= Constants::ETAPA['captura_reparto']) {


                if (!empty($antecedente)) {

                    // REGISTRAR PROCESO DISCIPLINARIO
                    // DESGLOSE
                    if ($proceso_disciplinario[0]->id_tipo_proceso == Constants::TIPO_DE_PROCESO['desglose']) {

                        if (!empty($proceso_disciplinario[0]->radicado_padre_desglose) && !empty($proceso_disciplinario[0]->vigencia_padre_desglose)) {
                            $requestProceso['radicado_padre_desglose'] = $proceso_disciplinario[0]->radicado_padre_desglose;
                            $requestProceso['vigencia_padre_desglose'] = $proceso_disciplinario[0]->vigencia_padre_desglose;
                        } else {
                            $error['estado'] = false;
                            $error['error'] = 'El proceso seleccionado es un desglose. Por favor indique radicado y vigencia padre.';
                            return json_encode($error);
                        }
                    }

                    // REGISTRAR PROCESO DISCIPLINARIO
                    // SIRIUS
                    if ($proceso_disciplinario[0]->id_tipo_proceso == Constants::TIPO_DE_PROCESO['correspondencia_sirius']) {

                        $requestProceso['radicado'] = substr($radicado, 8, 7);
                        $requestProceso['tipo_radicacion'] = substr($radicado, 5, 2);
                        $requestProceso['vigencia_origen'] = substr($radicado, 0, 4);
                    }
                    // REGISTRAR PROCESO DISCIPLINARIO
                    // OTROS
                    else {
                        $requestProceso['radicado'] = $radicado;
                        $requestProceso['vigencia'] = $vigencia;
                        $requestProceso['vigencia_origen'] = $vigencia;
                    }

                    $requestProceso['id_origen_radicado'] = $proceso_disciplinario[0]->radicado;
                    $requestProceso['id_tipo_proceso'] = $proceso_disciplinario[0]->id_tipo_proceso;
                    $requestProceso['antecedente'] = $antecedente[0]->descripcion;
                    $requestProceso['fecha_ingreso'] = $proceso_disciplinario[0]->created_at;
                    $requestProceso['id_etapa'] = $proceso_disciplinario[0]->id_etapa;
                    $requestProceso['id_funcionario_asignado'] = $proceso_disciplinario[0]->created_user;
                    $requestProceso['usuario_asignado'] = $proceso_disciplinario[0]->created_user;
                    $requestProceso['created_user'] = $proceso_disciplinario[0]->created_user;
                    $requestProceso['id_dependencia'] = $proceso_disciplinario[0]->id_dependencia_origen;
                    $requestProceso['id_dependencia_duena'] = $proceso_disciplinario[0]->id_dependencia_duena;
                    $requestProceso['estado'] = 1;
                    $requestProceso['usuario_actual'] = $usuario_actual[0]->name;

                    $model = new ProcesoDiciplinarioModel();
                    $rta = ProcesoDiciplinarioResource::make($model->create($requestProceso));
                    $array = json_decode(json_encode($rta));

                    $id_proceso_disciplinario = $array->id;

                    // CONSULTAR USUARIO ACTUAL


                    //$respuesta = $this->storeLogMigracion($requestProceso, $id_proceso_disciplinario, Constants::FASE['inicio_proceso_disciplinario'], $array->id);

                    // REGISTRAR ANTECEDENTES
                    for ($cont = 0; $cont < count($antecedente); $cont++) {

                        $requestAntencedente['id_proceso_disciplinario'] = $id_proceso_disciplinario;
                        $requestAntencedente['id_etapa'] = $requestProceso['id_etapa'];
                        $requestAntencedente['estado'] = 1;
                        $requestAntencedente['fecha_registro'] = $proceso_disciplinario[0]->created_at;
                        $requestAntencedente['descripcion'] = $antecedente[$cont]->descripcion;
                        $requestAntencedente['id_dependencia'] = $requestProceso['id_dependencia'];

                        $model = new AntecedenteModel();
                        $rta = AntecedenteResource::make($model->create($requestAntencedente));
                        $array = json_decode(json_encode($rta));

                        //$respuesta = $this->storeLogMigracion($requestProceso, $id_proceso_disciplinario, Constants::FASE['antecedentes'], $array->id);
                    }
                }

                if (!empty($datos_interesado)) {

                    // REGISTRAR DATOS INTERESADO
                    for ($cont = 0; $cont < count($datos_interesado); $cont++) {

                        $requestInteresado['id_proceso_disciplinario'] = $id_proceso_disciplinario;
                        $requestInteresado['id_tipo_interesao'] = $datos_interesado[$cont]->tipo_interesado;
                        $requestInteresado['id_etapa'] =  $requestProceso['id_etapa'];
                        $requestInteresado['id_tipo_sujeto_procesal'] = $datos_interesado[$cont]->tipo_sujeto_procesal;
                        $requestInteresado['tipo_documento'] = $datos_interesado[$cont]->tipo_documento;
                        $requestInteresado['numero_documento'] = $datos_interesado[$cont]->numero_documento;
                        $requestInteresado['primer_nombre'] = $datos_interesado[$cont]->primer_nombre;
                        $requestInteresado['segundo_nombre'] = $datos_interesado[$cont]->segundo_nombre;
                        $requestInteresado['primer_apellido'] = $datos_interesado[$cont]->primer_apellido;
                        $requestInteresado['segundo_apellido'] = $datos_interesado[$cont]->segundo_apellido;
                        $requestInteresado['id_departamento'] = $datos_interesado[$cont]->departamento;
                        $requestInteresado['id_ciudad'] = $datos_interesado[$cont]->ciudad;
                        $requestInteresado['direccion'] = $datos_interesado[$cont]->direccion;
                        $requestInteresado['id_localidad'] = $datos_interesado[$cont]->localidad;
                        $requestInteresado['email'] = $datos_interesado[$cont]->email;
                        $requestInteresado['telefono_celular'] = $datos_interesado[$cont]->telefono2;
                        $requestInteresado['telefono_fijo'] = $datos_interesado[$cont]->telefono;
                        $requestInteresado['id_sexo'] = $datos_interesado[$cont]->sexo;
                        $requestInteresado['id_orientacion_sexual'] = $datos_interesado[$cont]->orientacion_sexual;
                        $requestInteresado['nombre_entidad'] = $datos_interesado[$cont]->entidad;
                        $requestInteresado['id_dependencia'] = $requestProceso['id_dependencia'];
                        $requestInteresado['estado'] = 1;
                        $requestInteresado['id_funcionario'] = 1;

                        $model = new DatosInteresadoModel();
                        $rta = DatosInteresadoResource::make($model->create($requestInteresado));
                        $array = json_decode(json_encode($rta));

                        //$respuesta = $this->storeLogMigracion($requestProceso, $id_proceso_disciplinario, Constants::FASE['datos_interesado'], $array->id);
                    }
                }

                if ($proceso_disciplinario[0]->id_tipo_expediente != null) {

                    // REGISTRAR CLASIFICACION DEL RADICADO
                    $requestClasificacion['id_proceso_disciplinario'] = $id_proceso_disciplinario;
                    $requestClasificacion['id_etapa'] = $requestProceso['id_etapa'];
                    $requestClasificacion['id_tipo_expediente'] = $proceso_disciplinario[0]->id_tipo_expediente;

                    if ($proceso_disciplinario[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
                        $requestClasificacion['id_tipo_derecho_peticion'] = $proceso_disciplinario[0]->id_sub_tipo_expediente;
                    } else if ($proceso_disciplinario[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja'] || $proceso_disciplinario[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {
                        $requestClasificacion['id_tipo_queja'] = $proceso_disciplinario[0]->id_sub_tipo_expediente;
                    } else if ($proceso_disciplinario[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
                        $requestClasificacion['id_tipo_queja'] = Constants::TIPO_QUEJA['externa'];
                    } else if ($proceso_disciplinario[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']) {
                        $requestClasificacion['id_termino_respuesta'] = $proceso_disciplinario[0]->id_sub_tipo_expediente;
                    }

                    $requestClasificacion['id_dependencia'] = $requestProceso['id_dependencia'];
                    $requestClasificacion['estado'] =  Constants::ESTADOS['activo'];
                    $requestClasificacion['created_user'] = $requestProceso['created_user'];
                    $requestClasificacion['created_at'] =  $proceso_disciplinario[0]->created_at;

                    $model = new ClasificacionRadicadoModel();
                    $rta = ClasificacionRadicadoResource::make($model->create($requestClasificacion));
                    $array = json_decode(json_encode($rta));
                    $id_clasificacion =  $array->id;

                    //$this->storeLogMigracion($requestProceso, $id_proceso_disciplinario, Constants::FASE['clasificacion_radicado'], $array->id);
                }


                if (!empty($entidad)) {

                    $requestEntidad['id_proceso_disciplinario'] = $id_proceso_disciplinario;
                    $requestEntidad['id_etapa'] = $requestProceso['id_etapa'];
                    $requestEntidad['nombre_investigado'] = $entidad[0]->nombre_investigado;
                    $requestEntidad['id_dependencia'] = $requestProceso['id_dependencia'];
                    $requestEntidad['cargo'] = 1; //$entidad[0]->cargo_investigado;
                    $requestEntidad['estado'] = 1;
                    $requestEntidad['id_entidad'] = $entidad[0]->id_entidad;
                    $requestEntidad['observaciones'] = $entidad[0]->observaciones;
                    $requestEntidad['created_user'] = $requestProceso['created_user'];

                    $model = new EntidadInvestigadoModel();
                    $rta = EntidadInvestigadoResource::make($model->create($requestEntidad));
                    $array = json_decode(json_encode($rta));

                    //$this->storeLogMigracion($requestProceso, $id_proceso_disciplinario, Constants::FASE['entidad_investigado'], $array->id);

                    // SOPORTE RADICADO
                    //$this->storeLogMigracion($requestProceso, $id_proceso_disciplinario, Constants::FASE['soporte_radicado'], null);
                }
            }


            if (!empty($proceso_disciplinario) && $proceso_disciplinario[0]->id_etapa >= Constants::ETAPA['evaluacion'] && !empty($antecedente) && !empty($datos_interesado) && !empty($entidad)) {

                // CERRAR ETAPA CAPTURA Y REPARTO
                $requestCierreCR['id_proceso_disciplinario'] = $id_proceso_disciplinario;
                $requestCierreCR['id_funcionario_asignado'] = $requestProceso['id_etapa'];
                $requestCierreCR['id_etapa'] = 1;
                $requestCierreCR['created_user'] = $requestProceso['created_user'];
                $requestCierreCR['eliminado'] = 0;

                $model = new CierreEtapaModel();
                $rta = CierreEtapaResource::make($model->create($requestCierreCR));
                $array = json_decode(json_encode($rta));

                //$this->storeLogMigracion($requestProceso, $id_proceso_disciplinario, Constants::FASE['cierre_captura_reparto'], $array->id);

                // VALIDAR CLASIFICADO
                $requestValidarClasificacion['id_proceso_disciplinario'] = $id_proceso_disciplinario;
                $requestValidarClasificacion['id_etapa'] = 2;
                $requestValidarClasificacion['estado'] = 1;
                $requestValidarClasificacion['eliminado'] = 0;
                $requestValidarClasificacion['id_clasificacion_radicado'] = $id_clasificacion;

                $model = new ValidarClasificacionModel();
                $rta = ValidarClasificacionResource::make($model->create($requestValidarClasificacion));
                $array = json_decode(json_encode($rta));
                //$this->storeLogMigracion($requestProceso, $id_proceso_disciplinario, Constants::FASE['validacion_clasificacion'], $array->id);

                // EVALUACION

                $requestEvaluacion['id_proceso_disciplinario'] = $id_proceso_disciplinario;
                $requestEvaluacion['noticia_priorizada'] = 1;
                $requestEvaluacion['justificacion'] = 1;
                $requestEvaluacion['estado'] = 2;
                $requestEvaluacion['resultado_evaluacion'] = $proceso_disciplinario[0]->id_tipo_evaluacion;
                $requestEvaluacion['tipo_conducta'] = $proceso_disciplinario[0]->id_tipo_conducta;
                $requestEvaluacion['created_user'] = $id_clasificacion;

                $model = new EvaluacionModel();
                $rta = EvaluacionResource::make($model->create($requestEvaluacion));
                $array = json_decode(json_encode($rta));
                //$this->storeLogMigracion($requestProceso, $id_proceso_disciplinario, Constants::FASE['evaluacion'], $array->id);
            }

            if (!empty($proceso_disciplinario) && $proceso_disciplinario[0]->id_etapa >= Constants::ETAPA['evaluacion_pd'] && !empty($antecedente) && !empty($datos_interesado) && !empty($entidad)) {

                // CERRAR ETAPA DE EVALUACION
                $requestCierreEV['id_proceso_disciplinario'] = $id_proceso_disciplinario;
                $requestCierreEV['id_funcionario_asignado'] = $requestProceso['created_user'];
                $requestCierreEV['id_etapa'] = 2;
                $requestCierreEV['created_user'] = $requestProceso['created_user'];
                $requestCierreEV['eliminado'] = 0;

                $model = new CierreEtapaModel();
                $rta = CierreEtapaResource::make($model->create($requestCierreEV));
                $array = json_decode(json_encode($rta));

                //$this->storeLogMigracion($requestProceso, $id_proceso_disciplinario, Constants::FASE['cierre_evaluacion'], $array->id);

                // REGISTRAR ACTUACIONES
                if (!empty($actuaciones)) {
                    for ($cont = 0; $cont < count($actuaciones); $cont++) {
                        $requestActuacion['uuid_proceso_disciplinario'] = $id_proceso_disciplinario;
                        $requestActuacion['id_actuacion'] = $actuaciones[0]->tipo;
                        $requestActuacion['usuario_accion'] = auth()->user()->name;
                        $requestActuacion['id_estado_actuacion'] = 5;
                        $requestActuacion['documento_ruta'] = $actuaciones[0]->path;
                        $requestActuacion['estado'] = true;
                        $requestActuacion['id_etapa'] = $proceso_disciplinario[0]->id_etapa;
                        $requestActuacion['id_dependencia'] = $proceso_disciplinario[0]->id_dependencia_duena;
                        $requestActuacion['auto'] = $actuaciones[0]->autonumero;
                        $requestActuacion['eliminado'] = false;
                        $requestActuacion['created_user'] = auth()->user()->name;
                        $requestActuacion['created_at'] = $actuaciones[0]->fecha;

                        $model = new ActuacionesModel();
                        $rta = ActuacionesResource::make($model->create($requestActuacion));
                        $array = json_decode(json_encode($rta));

                        // $this->storeLogMigracion($requestProceso, $id_proceso_disciplinario, Constants::FASE['actuaciones_evaluacion_pd'], $array->id);
                    }
                }

                //DB::connection()->commit();
                //return $respuesta;
            }
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
     *
     */
    public function getInfoVersion($radicado, $vigencia)
    {
        $data_migracion['fechaRegistroDesde'] = null;
        $data_migracion['fechaRegistroHasta'] = null;
        $data_migracion['version'] = null;
        $data_migracion['vigencia'] = "";
        $data_migracion['numeroRadicado'] = $radicado;
        $data_migracion['nombreResponsable'] = "";
        $data_migracion['idResponsable'] = "";
        $data_migracion['dependencia'] = "";
        $data_migracion['idDependencia'] = "";
        $data_migracion['tipoInteresado'] = "";


        $respuesta = $this->buscarExpedientePorNumeroRadicado($data_migracion);

        $array = array();

        for ($cont = 0; $cont < count($respuesta['objectresponse']); $cont++) {

            if ($respuesta['objectresponse'][$cont]['vigencia'] ==  $vigencia) {

                $reciboDatos['attributes']['radicado'] = $radicado;
                $reciboDatos['attributes']['vigencia'] = $vigencia;
                $reciboDatos['attributes']['version'] = $respuesta['objectresponse'][$cont]['version'];

                array_push($array, $reciboDatos);

                $json['data'] = $array;
                return $reciboDatos['attributes']['version'];
            }
        }

        if (isEmpty($array)) {
            $error['estado'] = false;
            $error['error'] = 'Proceso disciplinario no encontrado en migración';
            return $error['error'];
        }
    }

    /**
     *
     */
    public function getInfoEstadoFasesMigradas($expediente, $vigencia)
    {

        $respuesta = $this->buscarDetalleExpediente($expediente, $vigencia);

        $array = array();

        // INICIO PROCESO DISCIPLINARIO
        $query = DB::select("select count(*) as total from temp_proceso_disciplinario where radicado = '" . $expediente . "' and vigencia = " . $vigencia);
        $query_fase = DB::select("select nombre, link_consulta_migracion as link_consulta from mas_fase where id = 7");

        $reciboDatos['attributes']['nombre'] = $query_fase[0]->nombre;
        $reciboDatos['attributes']['total_m'] = 0;
        $reciboDatos['attributes']['total_r'] = intval($query[0]->total);
        $reciboDatos['attributes']['link_consulta'] = $query_fase[0]->link_consulta;
        if ($reciboDatos['attributes']['total_r'] == 0) {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['red'];
        } else if ($reciboDatos['attributes']['total_r'] == 1) {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['green'];
        }
        array_push($array, $reciboDatos);


        // ANTECEDENTES
        $query = DB::select("select count(*) as total from temp_antecedentes where radicado = '" . $expediente . "' and vigencia = " . $vigencia);
        $query_fase = DB::select("select nombre, link_consulta_migracion as link_consulta from mas_fase where id = 1");

        $reciboDatos['attributes']['nombre'] = $query_fase[0]->nombre;
        $reciboDatos['attributes']['total_m'] = count($respuesta['objectresponse']['antecedentes']);
        $reciboDatos['attributes']['total_r'] = intval($query[0]->total);
        $reciboDatos['attributes']['link_consulta'] = $query_fase[0]->link_consulta;

        if ($reciboDatos['attributes']['total_r'] == 0) {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['red'];
        } else if ($reciboDatos['attributes']['total_r'] < $reciboDatos['attributes']['total_m']) {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['orange'];
        } else {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['green'];
        }
        array_push($array, $reciboDatos);

        // INTERESADOS
        $query = DB::select("select count(*) as total from temp_interesados where radicado = '" . $expediente . "' and vigencia = " . $vigencia);
        $query_fase = DB::select("select nombre, link_consulta_migracion as link_consulta from mas_fase where id = 2");

        $reciboDatos['attributes']['nombre'] = $query_fase[0]->nombre;
        $reciboDatos['attributes']['total_m'] = count($respuesta['objectresponse']['interesados']);
        $reciboDatos['attributes']['total_r'] = intval($query[0]->total);
        $reciboDatos['attributes']['link_consulta'] = $query_fase[0]->link_consulta;

        if ($reciboDatos['attributes']['total_r'] == 0) {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['red'];
        } else if ($reciboDatos['attributes']['total_r'] < $reciboDatos['attributes']['total_m']) {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['orange'];
        } else {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['green'];
        }
        array_push($array, $reciboDatos);

        // ENTIDAD DEL INVESTIGADO
        $query = DB::select("select count(*) as total from temp_entidades where radicado = '" . $expediente . "' and vigencia = " . $vigencia);
        $query_fase = DB::select("select nombre, link_consulta_migracion as link_consulta from mas_fase where id = 4");

        $reciboDatos['attributes']['nombre'] = $query_fase[0]->nombre;
        $reciboDatos['attributes']['total_m'] = count($respuesta['objectresponse']['entidadesInvestigados']);
        $reciboDatos['attributes']['total_r'] = intval($query[0]->total);
        $reciboDatos['attributes']['link_consulta'] = $query_fase[0]->link_consulta;

        if ($reciboDatos['attributes']['total_r'] == 0) {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['red'];
        } else if ($reciboDatos['attributes']['total_r'] < $reciboDatos['attributes']['total_m']) {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['orange'];
        } else {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['green'];
        }
        array_push($array, $reciboDatos);

        // ACTUACIONES
        $query = DB::select("select count(*) as total from temp_actuaciones where radicado = '" . $expediente . "' and vigencia = " . $vigencia);
        $query_fase = DB::select("select nombre, link_consulta_migracion as link_consulta from mas_fase where id = 16");

        $reciboDatos['attributes']['nombre'] = $query_fase[0]->nombre;
        $reciboDatos['attributes']['total_m'] = count($respuesta['objectresponse']['actuaciones']);
        $reciboDatos['attributes']['total_r'] = intval($query[0]->total);
        $reciboDatos['attributes']['link_consulta'] = $query_fase[0]->link_consulta;

        if ($reciboDatos['attributes']['total_r'] == 0) {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['red'];
        } else if ($reciboDatos['attributes']['total_r'] < $reciboDatos['attributes']['total_m']) {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['orange'];
        } else {
            $reciboDatos['attributes']['semaforizacion'] = Constants::SEMAFORIZACION['green'];
        }
        array_push($array, $reciboDatos);

        $json['data'] = $array;
        return json_encode($json);
    }

    public function buscadorGeneral(BuscadorFormRequest $request)
    {

        // Se capturan los datos
        $datosRequest = $request->validated()["data"]["attributes"];

        // CONSULTAR EN MIGRACION

        $data_migracion['fechaRegistroDesde'] = null;
        $data_migracion['fechaRegistroHasta'] = null;
        $data_migracion['version'] = null;
        $data_migracion['vigencia'] = "";
        $data_migracion['numeroRadicado'] = $datosRequest["radicado"];
        $data_migracion['nombreResponsable'] = "";
        $data_migracion['idResponsable'] = "";
        $data_migracion['dependencia'] = "";
        $data_migracion['idDependencia'] = "";
        $data_migracion['tipoInteresado'] = "";

        $rta_migracion = $this->buscarExpedientePorNumeroRadicado($data_migracion);

        // SI NO SE ENCONTRO EL RADICADO EN EL SISTEMA DE MIGRACIÓN
        /*if ($rta_migracion->estado == false) {
            $error['estado'] = false;
            $error['error'] = 'NO SE ENCONTRO NINGUNA COINCIDENCIA';
            return json_encode($error);
        }*/

        $arr = array();

        for ($cont = 0; $cont < count($rta_migracion['objectresponse']); $cont++) {

            if (str_contains($rta_migracion['objectresponse'][$cont]['idRegistro'], 'V3')) {
                $version = "EXCEL";
            } else {
                $version = $rta_migracion['objectresponse'][$cont]['version'];
            }

            if ($rta_migracion['objectresponse'][$cont]['vigencia'] != 'NA') {

                $rta_detalle_expediente = $this->buscarDetalleExpediente($datosRequest["radicado"], $rta_migracion['objectresponse'][$cont]['vigencia']);

                //VALIDAR SI EL PROCESO YA FUE MIGRADO.
                $validar_proceso_disciplinario = DB::select("select radicado from proceso_disciplinario where radicado = '" . $datosRequest["radicado"] . "' and vigencia = " . $rta_migracion['objectresponse'][$cont]['vigencia']);

                if (!empty($validar_proceso_disciplinario)) {
                    $observacion =  "EL PROCESO YA FUE MIGRADO.";
                } else {
                    $observacion =  "";
                }

                array_push(
                    $arr,
                    array(
                        "type" => "buscador",
                        "attributes" => array(
                            "Id" => $rta_migracion['objectresponse'][$cont]['id'],
                            "Numero_expediente" => $rta_migracion['objectresponse'][$cont]['numeroRadicado'],
                            "Version" => $version,
                            "Dependencia" => $rta_migracion['objectresponse'][$cont]['dependencia'],
                            "Etapa_expediente" => $rta_detalle_expediente['objectresponse']['etapaActual'],
                            "Asunto_del_expediente" => $rta_detalle_expediente['objectresponse']['antecedentes'] == null ? null : $rta_detalle_expediente['objectresponse']['antecedentes'][0]['hechos'],
                            "Nombre_interesado" => $rta_detalle_expediente['objectresponse']['interesados'] == null ? null : $rta_detalle_expediente['objectresponse']['interesados'][0]['nombreCompleto'],
                            "Estado_expediente" => $rta_detalle_expediente['objectresponse']['estadoActual'],
                            "migracion" => 1,
                            "observacion" => $observacion,
                            "proceso_disciplinario" => array(
                                "type" => "proceso_disciplinario",
                                "attributes" => array(
                                    //"Auto" => $rta_migracion['objectresponse'][$cont]['idRegistro'],
                                    "vigencia" => $rta_migracion['objectresponse'][$cont]['vigencia'],
                                )
                            )
                        )
                    )
                );
            } else {

                array_push(
                    $arr,
                    array(
                        "type" => "buscador",
                        "attributes" => array(
                            "Id" => $rta_migracion['objectresponse'][$cont]['id'],
                            "Numero_expediente" => $rta_migracion['objectresponse'][$cont]['numeroRadicado'],
                            "Version" => $version,
                            "Dependencia" => $rta_migracion['objectresponse'][$cont]['dependencia'],
                            "Etapa_expediente" => "",
                            "Asunto_del_expediente" => "",
                            "Nombre_interesado" => "",
                            "Estado_expediente" =>  "",
                            "migracion" => -1,
                            "observacion" => "",
                            "proceso_disciplinario" => array(
                                "type" => "proceso_disciplinario",
                                "attributes" => array(
                                    //"Auto" => $rta_migracion['objectresponse'][$cont]['idRegistro'],
                                    "vigencia" => $rta_migracion['objectresponse'][$cont]['vigencia'],
                                )
                            )
                        )
                    )
                );
            }
        }

        $rtaFinal = array(
            "data" => $arr
        );

        DB::connection()->commit();
        return json_encode($rtaFinal);
    }


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
        //$query_disc_solicitudes = DB::select("SELECT num_solicitud, vigencia, tabla FROM DISC_SOLICITUDES");

        $query_disc_solicitudes = DB::select("SELECT num_solicitud, vigencia, tabla FROM DISC_SOLICITUDES WHERE num_solicitud  IN (33881, 34527,37987,38312,321676,322805,328835,328940,335851,336162,337246,337246,342199,344557, 354292,355254,356190,369519,372064,382897,390477,3385405,3477137,3495433,3500531)");

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
                $proceso[0]->id_dependencia = $this->getDependenciaAmbientePruebas($proceso[0]->id_dependencia);
                $etapa[0]->dependencia_finaliza =  $this->getDependenciaAmbientePruebas($etapa[0]->dependencia_finaliza);
                // FIN SOLO APLICA PARA PRUEBAS DEBE ELIMINARSE CUANDO SE PASE A PRODUCCION


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
                $datosProcesoDisciplinario['id_dependencia'] = $this->getDependenciaAmbientePruebas($proceso[0]->id_dependencia);
                $datosProcesoDisciplinario['id_dependencia_actual'] = $this->getDependenciaAmbientePruebas($etapa[0]->dependencia_finaliza);
                $datosProcesoDisciplinario['id_dependencia_duena'] = $this->getDependenciaAmbientePruebas($etapa[0]->dependencia_finaliza);
                $datosProcesoDisciplinario['migrado'] =  true;
                $datosProcesoDisciplinario['fuente_bd'] =  true;
                $datosProcesoDisciplinario['fuente_excel'] =  false;

                $model_proceso_disciplinario = new ProcesoDiciplinarioModel();
                $rta_proceso = ProcesoDiciplinarioResource::make($model_proceso_disciplinario->create($datosProcesoDisciplinario));
                $array_proceso = json_decode(json_encode($rta_proceso));


                $datosProcesoDisciplinario['uuid'] =  $rta_proceso->uuid;
                if ($query_disc_solicitudes[$cont]->tabla == 'DISC_INGRESO_DESGLOSE') {
                    $model_proceso_disciplinario = new ProcesoDesgloseModel();
                    $rta_proceso = ProcesoDesgloseResource::make($model_proceso_disciplinario->create($datosProcesoDisciplinario));
                } else if ($query_disc_solicitudes[$cont]->tabla == 'DISC_INGRESO_CORRESPONDENCIA') {
                    $model_proceso_disciplinario = new ProcesoSiriusModel();
                    $rta_proceso = ProcesoSiriusResource::make($model_proceso_disciplinario->create($datosProcesoDisciplinario));
                } else if ($query_disc_solicitudes[$cont]->tabla == 'DISC_INGRESO_SINPROC') {
                    $model_proceso_disciplinario = new ProcesoSinprocModel();
                    $rta_proceso = ProcesoSinprocResource::make($model_proceso_disciplinario->create($datosProcesoDisciplinario));
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
                        $datosAntecedente['id_dependencia'] = $this->getDependenciaAmbientePruebas($antecedentes[$cont_antecedente]->id_dependencia);
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
                        //error_log("Antecedentes: " . json_encode($rta_antecedentes));

                        //error_log("array_proceso->id  " . $array_proceso->id);
                        // error_log("array_antecedentes->id " . $array_antecedentes->id);






                        //REGISTRAR EN EL LOG PROCESO DISCIPLINARIO
                        //}
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

                                        //error_log("id_entidad_publica" . $empresa[0]->id_entidad_publica);
                                        //error_log("id_dependencia_personeria" . $empresa[0]->id_dependiencia_personeria);

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
                            $id_etapa = Constants::ETAPA['evaluacion'];
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
            }
        }
    }
}
