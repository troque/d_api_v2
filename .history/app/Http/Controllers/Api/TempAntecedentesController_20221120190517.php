<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TempAntecedentesFormRequest;
use App\Http\Resources\TempAntecedentes\TempAntecedentesCollection;
use App\Http\Resources\TempAntecedentes\TempAntecedentesResource;
use App\Models\TempActuacionesModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TempAntecedentesController extends Controller
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
        return TempAntecedentesCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TempAntecedentesFormRequest $request)
    {

        try {

            $datosRequest = $request->validated();

            if ($datosRequest['estado'] = "FINALIZADO") {
                $datosRequest['estado'] = 2;
            } else {
                $datosRequest['estado'] = 1;
            }

            $datosRequest['created_at'] = $datosRequest['fecha_registro'];
            $usuario = DB::select("select name from users where id = " . $datosRequest['registrado_por']);

            if ($usuario != null) {
                $datosRequest['created_user'] = $usuario[0]->name;
            }

            $respuesta = TempAntecedentesResource::make($this->repository->create($datosRequest));

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
        return TempAntecedentesResource::make($this->repository->find($id));
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
}
