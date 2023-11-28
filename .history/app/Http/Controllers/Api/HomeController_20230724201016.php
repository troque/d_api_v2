<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActuacionPorSemaforoFormRequest;
use App\Http\Resources\ActuacionPorSemaforo\ActuacionPorSemaforoCollection;
use App\Http\Resources\ActuacionPorSemaforo\ActuacionPorSemaforoResource;
use App\Models\ActuacionesModel;
use App\Models\ActuacionPorSemaforoModel;
use App\Models\DiasNoLaboralesModel;

use Illuminate\Support\Facades\DB;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Método contructor
     *
     * @param RepositoryGeneric $repository
     */
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new AntecedenteModel());
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


    public function DocumentosPendientesDeFirmaPorUsuarioLimitTen($id_user)
    {
        try {
            $this->repository->setModel(new FirmaActuacionesModel());
            $query = $this->repository->customQuery(function ($model) use ($id_user) {
                return $model
                    ->where('id_user', $id_user)
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
    public function store()
    {
    }

    public function show()
    {
    }

    /**
     *
     */
    public function update()
    {
    }
}
