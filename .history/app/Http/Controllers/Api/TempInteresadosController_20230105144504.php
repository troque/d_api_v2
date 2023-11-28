<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TempInteresadosFormRequest;
use App\Http\Resources\TempInteresados\TempInteresadosCollection;
use App\Http\Resources\TempInteresados\TempInteresadosResource;
use App\Models\TempInteresadosModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TempInteresadosController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TempInteresadosModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TempInteresadosCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TempInteresadosFormRequest $request)
    {

        try {

            $datosRequest = $request->validated()["data"]["attributes"];

            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model->where('radicado', $datosRequest['radicado'])
                    ->where('vigencia', $datosRequest['vigencia'])
                    ->where('item', $datosRequest['item'])
                    ->get();
            });

            if (count($query) == 0) {
                $respuesta = TempInteresadosResource::make($this->repository->create($datosRequest));
            } else {
                $respuesta = TempInteresadosModel::where('radicado', $datosRequest['radicado'])
                    ->where('vigencia', $datosRequest['vigencia'])
                    ->where('item', $datosRequest['item'])
                    ->update([
                        'tipo_interesado' => $datosRequest['tipo_interesado'],
                        'tipo_sujeto_procesal' => $datosRequest['tipo_sujeto_procesal'],
                        'primer_nombre' => $datosRequest['primer_nombre'],
                        'segundo_nombre' => $datosRequest['segundo_nombre'],
                        'primer_apellido' => $datosRequest['primer_apellido'],
                        'segundo_apellido' => $datosRequest['segundo_apellido'],
                        'tipo_documento' => $datosRequest['tipo_documento'],
                        'numero_documento' => $datosRequest['numero_documento'],
                        'email' => $datosRequest['email'],
                        'telefono' => $datosRequest['telefono'],
                        'telefono2' => $datosRequest['telefono2'],
                        'cargo' => $datosRequest['cargo'],
                        'orientacion_sexual' => $datosRequest['orientacion_sexual'],
                        'sexo' => $datosRequest['sexo'],
                        'direccion' => $datosRequest['direccion'],
                        'departamento' => $datosRequest['departamento'],
                        'ciudad' => $datosRequest['ciudad'],
                        'localidad' => $datosRequest['localidad'],
                        'entidad' => $datosRequest['entidad'],
                        'sector' => $datosRequest['sector']
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
        return TempInteresadosResource::make($this->repository->find($id));
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

    public function getTempInteresados($radicado, $vigencia, $item)
    {
        $query = $this->repository->customQuery(function ($model) use ($radicado, $vigencia, $item) {
            return $model->where('radicado', $radicado)
                ->where('vigencia', $vigencia)
                ->where('item', $item)
                ->get();
        });

        return TempInteresadosCollection::make($query);
    }
}
