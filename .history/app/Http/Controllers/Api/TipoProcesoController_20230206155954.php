<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneroFormRequest;
use App\Http\Resources\TipoProceso\TipoProcesoCollection;
use App\Http\Resources\TipoProceso\TipoProcesoResource;
use App\Models\TipoProcesoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TipoProcesoController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoProcesoModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $query = TipoProcesoModel::query();
        $query = $query->select('mas_tipo_proceso.id', 'mas_tipo_proceso.nombre', 'mas_tipo_proceso.estado')->orderBy('mas_tipo_proceso.id', 'asc')->get();

        return TipoProcesoCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TipoProcesoResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GeneroFormRequest $request, $id)
    {
        return TipoProcesoResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->noContent();
    }

    public function getTipoProcesoActivos()
    {
        $query = $this->repository->customQuery(function ($model) {
            return $model->where('estado', 1)
                ->orderBy("id", "asc")
                ->get();
        });

        return TipoProcesoCollection::make($query);
    }
}
