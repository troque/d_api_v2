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
        $query = DB::select("SELECT ACTUACION_POR_SEMAFORO_ID_SEQ.CURRVAL FROM dual");
        //DB::select("ALTER SEQUENCE ACTUACION_POR_SEMAFORO_ID_SEQ START WITH $query[0]->");
        //DB::connection()->commit();

    }
}
