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
        $procesos_activos = DB::select(
            "
                SELECT 
                    count(*) AS cant_procesos_activos
                FROM (
                    SELECT DISTINCT 
                        pd.uuid
                    FROM 
                        proceso_disciplinario pd
                    LEFT JOIN log_proceso_disciplinario lpd ON lpd.id_proceso_disciplinario = pd.uuid
                    WHERE lpd.id_funcionario_actual = '" . auth()->user()->name . "'
                    AND pd.estado = " . Constants::ESTADO_PROCESO_DISCIPLINARIO['activo'] ."
                )
            "
        );

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

    /**
     *
     */
    public function getProcesosPorTipoExpediente()
    {
        // CANTIDAD TOTAL DE PROCOESOS
        $cantTotal = DB::select("SELECT count(*) total
        FROM log_proceso_disciplinario
        WHERE id_funcionario_actual = '" . auth()->user()->name . "'");

        $json['data']['attributes']['total'] = $cantTotal[0]->total;

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

        $json['data']['attributes']['poderPreferente'] = $cantPoderPreferente[0]->total;

        /**
         *QUEJA
         **/
        //TOTAL
        $cantQueja = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . auth()->user()->name . "' and cr.id_tipo_expediente = 3  and cr.estado = 1");

        $json['data']['attributes']['queja'] = $cantQueja[0]->total;

        /**
         *TUTELA
         **/
        // TOTAL
        $cantTutela = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . auth()->user()->name . "' and cr.id_tipo_expediente = 4  and cr.estado = 1");

        $json['data']['attributes']['tutela'] = $cantTutela[0]->total;

        /**
         *PROCESO DISCIPLINARIO
         **/
        $cantProcesoDisciplinario = DB::select("select count(*) total
        from log_proceso_disciplinario lpd
        inner join clasificacion_radicado cr on cr.id_proceso_disciplinario = lpd.id_proceso_disciplinario
        where lpd.id_funcionario_actual = '" . auth()->user()->name . "' and cr.id_tipo_expediente = 5 and cr.estado = 1");

        $json['data']['attributes']['procesoDisciplinario'] = $cantProcesoDisciplinario[0]->total;

        $sin_clasificacion = $cantTotal[0]->total - ($cantDerechoPeticion[0]->total + $cantPoderPreferente[0]->total + $cantQueja[0]->total + $cantTutela[0]->total + $cantProcesoDisciplinario[0]->total);
        $json['data']['attributes']['sinClasificacion'] = $sin_clasificacion;

        return json_encode($json);
    }

    /**
     *
     */
    public function getProcesosPorEtapa()
    {

        // ETAPAS
        $etapas = DB::select("SELECT
            me.id,
            me.nombre
            FROM mas_etapa me
            WHERE me.estado = 1");


        $array = array();

        for ($cont = 0; $cont < count($etapas); $cont++) {

            // CANTIDAD TOTAL DE PROCESOS


            $linea = "SELECT
            count(pd.id_etapa) AS cant_etapa
            FROM log_proceso_disciplinario lpd
            INNER JOIN proceso_disciplinario pd ON lpd.id_proceso_disciplinario = pd.uuid
            INNER JOIN mas_etapa me ON me.id = pd.id_etapa
            WHERE lpd.id_funcionario_actual = '" . auth()->user()->name . "'. AND  pd.id_etapa = " . $etapas[$cont]->id;

            error_log($linea);


            $query = DB::select("SELECT
            count(pd.id_etapa) AS cant_etapa
            FROM log_proceso_disciplinario lpd
            INNER JOIN proceso_disciplinario pd ON lpd.id_proceso_disciplinario = pd.uuid
            INNER JOIN mas_etapa me ON me.id = pd.id_etapa
            WHERE lpd.id_funcionario_actual = '" . auth()->user()->name . "' AND  pd.id_etapa = " . $etapas[$cont]->id);

            $reciboDatos['id_etapa'] = $etapas[$cont]->id;
            $reciboDatos['nombre_etapa'] = $etapas[$cont]->nombre;

            if (count($query) > 0) {
                $reciboDatos['cant_etapa'] = $query[0]->cant_etapa;
            } else {
                $reciboDatos['cant_etapa'] = 0;
            }


            array_push($array, $reciboDatos);
        }

        $json['data'] = $array;
        //$json = $array;
        return json_encode($json);



        //for ($cont = 0; $cont < count($procesos_etapa); $cont++) {

        // $json['data']['attributes'][$procesos_etapa[$cont]->id_etapa] = $procesos_etapa[$cont]->cant_etapa;
        //$json['data']['attributes']['nombre_etapa'] = $procesos_etapa[$cont]->nombre;

        //$reciboDatos['id_etapa'] = $procesos_etapa[$cont]->id_etapa;
        //$reciboDatos['nombre_etapa'] = $procesos_etapa[$cont]->nombre;
        //$reciboDatos['cant_etapa'] = $procesos_etapa[$cont]->cant_etapa;

        //array_push($array, $reciboDatos);
        //}

        //$json = $array;
        //return json_encode($json);
    }
}
