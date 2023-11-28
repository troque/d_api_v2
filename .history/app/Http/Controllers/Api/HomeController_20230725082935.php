<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActuacionPorSemaforoFormRequest;
use App\Http\Resources\ActuacionPorSemaforo\ActuacionPorSemaforoCollection;
use App\Http\Resources\ActuacionPorSemaforo\ActuacionPorSemaforoResource;
use App\Http\Resources\FirmaActuaciones\DocumentosParaFirmaCollection;
use App\Http\Utilidades\Constants;
use App\Models\ActuacionesModel;
use App\Models\FirmaActuacionesModel;
use Illuminate\Support\Facades\DB;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    private $repository;

    /**
     * Método contructor
     *
     * @param RepositoryGeneric $repository
     */
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new FirmaActuacionesModel());
    }

    /**
     *
     */
    public function getDashboard()
    {
        $procesos_activos = DB::select("SELECT count(*) AS cant_procesos_activos
            FROM log_proceso_disciplinario lpd
            WHERE lpd.id_funcionario_actual = '" . auth()->user()->name . "'");

        $documentos_pendientes_firma = DB::select("SELECT count(*) as cant_firma_pendiente
            FROM firma_actuaciones mtf
            WHERE mtf.id_user = " . auth()->user()->id . " and mtf.estado = 1");

        $reciboDatos['attributes']['cat_procesos_activos'] = $procesos_activos[0]->cant_procesos_activos;
        $reciboDatos['attributes']['cat_documentos_sin_firmar'] = $documentos_pendientes_firma[0]->cant_firma_pendiente;

        $array = array();
        array_push($array, $reciboDatos);

        $json['data'] = $array;
        return json_encode($json);
    }


    /**
     *
     */
    public function DocumentosPendientesDeFirmaPorUsuario()
    {
        try {
            $this->repository->setModel(new FirmaActuacionesModel());
            $query = $this->repository->customQuery(function ($model) {
                return $model
                    ->where('id_user', auth()->user()->id)
                    ->where('estado', "=", Constants::ESTADO_FIRMA_MECANICA['pendiente_de_firma'])
                    ->whereNull('eliminado')
                    ->orWhere('eliminado', '0')
                    ->orderBy('created_at', 'desc')
                    ->skip(0)->take(10)
                    ->get();
            });
            return DocumentosParaFirmaCollection::make($query);
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

    public function getProcesosPorTipoExpediente()
    {

        /**
         * DERECHO DE PETICION
         **/
        $cantDerechoPeticion = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . auth()->user()->name . "' and cr.id_tipo_expediente = 1 and cr.estado = 1");

        $json['data']['attributes']['derechoPeticion'] = $cantDerechoPeticion[0]->total;

        /**
         *PODER PREFERENTE
         **/
        $cantPoderPreferente = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . auth()->user()->name . "' and cr.id_tipo_expediente = 2  and cr.estado = 1");

        $json['data']['attributes']['queja'] = $cantPoderPreferente[0]->total;

        /**
         *QUEJA
         **/
        //TOTAL
        $cantQueja = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . auth()->user()->name . "' and cr.id_tipo_expediente = 3  and cr.estado = 1");

        $json['data']['attributes']['poderPreferente'] = $cantQueja[0]->total;

        /**
         *TUTELA
         **/
        // TOTAL
        $cantTutela = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . auth()->user()->name . "' and cr.id_tipo_expediente = 4  and cr.estado = 1");

        $json['data']['attributes']['poderPreferente'] = $cantTutela[0]->total;

        /**
         *PROCESO DISCIPLINARIO
         **/
        $cantProcesoDisciplinario = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . auth()->user()->name . "' and cr.id_tipo_expediente = 5 and cr.estado = 1");

        $json['data']['attributes']['procesoDisciplinario'] = $cantProcesoDisciplinario[0]->total;

        $reciboDatosPD['total'] =  $cantProcesoDisciplinario[0]->total;

        $json['data']['attributes']['tutela'] = $reciboDatosT;
        $json['data']['attributes']['procesoDisciplinario'] = $reciboDatosPD;



        return json_encode($json);
    }
}
