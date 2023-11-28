<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TempAntecedentesFormRequest;
use App\Http\Resources\TempAntecedentes\TempAntecedentesCollection;
use App\Http\Resources\TempAntecedentes\TempAntecedentesResource;
use App\Models\TempAntecedentesModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TempAntecedentesController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TempAntecedentesModel());
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

            $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
                return $model->where('radicado', $datosRequest['radicado'])
                    ->where('vigencia', $datosRequest['vigencia'])
                    ->where('item', $item)
                    ->get();
            });

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


    public function getTempAntecedentes($radicado, $vigencia, $item)
    {
        $query = $this->repository->customQuery(function ($model) use ($radicado, $vigencia, $item) {
            return $model->where('radicado', $radicado)
                ->where('vigencia', $vigencia)
                ->where('item', $item)
                ->get();
        });

        return TempAntecedentesCollection::make($query);
    }
}
