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
use App\Http\Resources\DatosInteresado\DatosInteresadoResource;
use App\Http\Resources\EntidadInvestigado\EntidadInvestigadoResource;
use App\Http\Resources\Evaluacion\EvaluacionResource;
use App\Http\Resources\ProcesoDiciplinario\ProcesoDiciplinarioResource;
use App\Http\Resources\RemisionQueja\RemisionQuejaResource;
use App\Http\Resources\ValidarClasificacion\ValidarClasificacionResource;
use App\Http\Utilidades\Constants;
use App\Models\ActuacionesModel;
use App\Models\AntecedenteModel;
use App\Models\CierreEtapaModel;
use App\Models\ClasificacionRadicadoModel;
use App\Models\DatosInteresadoModel;
use App\Models\EntidadInvestigadoModel;
use App\Models\EvaluacionModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\RemisionQuejaModel;
use App\Models\ValidarClasificacionModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class SecuenciaController extends Controller
{

    public function iniciarSecuencia()
    {
        $query = DB::select("SELECT ACTUACION_POR_SEMAFORO_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE ACTUACION_POR_SEMAFORO_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);


        $query = DB::select("SELECT ASIGNACION_PROCESO_DISCIPLINAR.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE ASIGNACION_PROCESO_DISCIPLINAR RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT CIERRE_ETAPA_CONFIGURACION_ID_.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE CIERRE_ETAPA_CONFIGURACION_ID_ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT CONDICION_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE CONDICION_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT EVALUACION_FASE_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE EVALUACION_FASE_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT LOG_CONSULTAS_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE LOG_CONSULTAS_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_ACTUACIONES_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_ACTUACIONES_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_BUSQUEDA_EXPEDIENTE_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_BUSQUEDA_EXPEDIENTE_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_CARATULAS_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_CARATULAS_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_CIUDAD_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_CIUDAD_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_CONSECUTIVO_ACTUACIONES_ID.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_CONSECUTIVO_ACTUACIONES_ID RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_CONSECUTIVO_DESGLOSE_ID_SE.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_CONSECUTIVO_DESGLOSE_ID_SE RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_DEPARTAMENTO_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_DEPARTAMENTO_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_DEPENDENCIA_ACCESO_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_DEPENDENCIA_ACCESO_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_DIAS_NO_LABORALES_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_DIAS_NO_LABORALES_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_DIRECCION_BIS_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_DIRECCION_BIS_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_DIRECCION_COMPLEMENTO_ID_S.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_DIRECCION_COMPLEMENTO_ID_S RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_DIRECCION_LETRAS_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_DIRECCION_LETRAS_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_DIRECCION_NOMENCLATURA_ID_.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_DIRECCION_NOMENCLATURA_ID_ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_DIRECCION_ORIENTACION_ID_S.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_DIRECCION_ORIENTACION_ID_S RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_ENTIDAD_PERMITIDA_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_ENTIDAD_PERMITIDA_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_ESTADO_ACTUACIONES_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_ESTADO_ACTUACIONES_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_ESTADO_PROCESO_DISCIPLINAR.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_ESTADO_PROCESO_DISCIPLINAR RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_ESTADO_REPARTO_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_ESTADO_REPARTO_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_ETAPA_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_ETAPA_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_EVENTO_INICIO_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_EVENTO_INICIO_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_FASE_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_FASE_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_FORMATO_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_FORMATO_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_FUNCIONALIDAD_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_FUNCIONALIDAD_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_GENERO_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_GENERO_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_GRUPO_TRABAJO_SECRETARIA_C.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_GRUPO_TRABAJO_SECRETARIA_C RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_LOCALIDAD_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_LOCALIDAD_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_MODULO_GRUPO_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_MODULO_GRUPO_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_MODULO_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_MODULO_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_ORDEN_FUNCIONARIO_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_ORDEN_FUNCIONARIO_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_ORIENTACION_SEXUAL_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_ORIENTACION_SEXUAL_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_ORIGEN_RADICADO_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_ORIGEN_RADICADO_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_PARAMETRO_CAMPOS_CARATULA_.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_PARAMETRO_CAMPOS_CARATULA_ RESTART START WITH " . $query[0]->NEXTVAL);

        $query = DB::select("SELECT MAS_PARAMETRO_CAMPOS_ID_SEQ.NEXTVAL FROM dual");
        DB::select("ALTER SEQUENCE MAS_PARAMETRO_CAMPOS_ID_SEQ RESTART START WITH " . $query[0]->NEXTVAL);




        //DB::connection()->commit();

    }
}
