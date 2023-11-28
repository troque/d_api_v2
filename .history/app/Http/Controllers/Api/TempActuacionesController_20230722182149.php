<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TempActuacionesFormRequest;
use App\Http\Resources\TempActuaciones\TempActuacionesCollection;
use App\Http\Resources\TempActuaciones\TempActuacionesResource;
use App\Models\TempActuacionesModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

use function PHPUnit\Framework\isEmpty;

class TempActuacionesController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TempActuacionesModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TempActuacionesCollection::make($this->repository->paginate($request->limit ?? 20));
    }



    public function store(TempActuacionesFormRequest $request)
    {
        try {

            $datosRequest = $request->validated()["data"]["attributes"];

            $respuesta_subida = $this->subirDocumentoTemporal($datosRequest);

            if ($respuesta_subida && !$respuesta_subida->estado) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = $respuesta_subida->error ? $respuesta_subida->error : 'No se puede subir documento, si el error, persiste comuniquese con el Administrador';
                return $error;
            }

            $datosRequest['path'] = $respuesta_subida->pathUrl;

            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model->where('radicado', $datosRequest['radicado'])
                    ->where('vigencia', $datosRequest['vigencia'])
                    ->where('item', $datosRequest['item'])
                    ->get();
            });

            if (count($query) == 0) {
                $respuesta = TempActuacionesResource::make($this->repository->create($datosRequest));
            } else {
                $respuesta = TempActuacionesModel::where('radicado', $datosRequest['radicado'])
                    ->where('vigencia', $datosRequest['vigencia'])
                    ->where('item', $datosRequest['item'])
                    ->update([
                        'nombre' => $datosRequest['nombre'],
                        'tipo' => $datosRequest['tipo'],
                        'autonumero' => $datosRequest['autonumero'],
                        'fecha' => $datosRequest['fecha'],
                        'fechatermino' => $datosRequest['fechatermino'],
                        'instancia' => $datosRequest['instancia'],
                        'decision' => $datosRequest['decision'],
                        'observacion' => $datosRequest['observacion'],
                        'terminoMonto' => $datosRequest['terminomonto'],

                    ]);
            }

            return $respuesta;
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TempActuacionesResource::make($this->repository->find($id));
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

    public function getTempActuaciones($radicado, $vigencia, $item)
    {
        $query = $this->repository->customQuery(function ($model) use ($radicado, $vigencia, $item) {
            return $model->where('radicado', $radicado)
                ->where('vigencia', $vigencia)
                ->where('item', $item)
                ->get();
        });

        return TempActuacionesCollection::make($query);
    }

    public function subirDocumentoTemporal($datosRequest)
    {
        try {

            if (!env('SUBIR_DOCUMENTACION_SIRIUS') && !env('SUBIR_DOCUMENTACION_LOCAL')) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "ERROR EN LA CONFIGURACIÓN DEL ENV POR FAVOR COMUNíQUESE CON EL ADMINISTRADOR";
                return $error;
            }

            $path = null;
            $path = storage_path() . '/files/temp_actuaciones';

            if (!env('OMITIR_SUBIDA_ARCHIVO')) {
                /*Guardar File*/
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $path = $path . '/' . $datosRequest['radicado'] . '.pdf';

                $documentos[0]['path'] = $path;

                $b64 = $datosRequest['documentoBase64'];
                $bin = base64_decode($b64, true);
                file_put_contents($path, $bin);
            }
            $result = new stdClass;
            $result->estado = true;
            $result->pathUrl = '/files/temp_actuaciones/' . $datosRequest['radicado'] . '.pdf';

            return $result;
        } catch (\Exception $e) {
            error_log($e);
            dd($e);

            if ((strpos($e->getMessage(), 'Network') !== false) || (strpos($e->getMessage(), 'Request Entity Too Large') !== false)) {
                $result = new stdClass;
                $result->estado = false;
                return $result;
            }

            $result = new stdClass;
            $result->estado = false;
            return $result;
        }
    }
}
