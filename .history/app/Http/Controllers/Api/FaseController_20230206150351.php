<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FaseFormRequest;
use App\Http\Resources\Fase\FaseCollection;
use App\Http\Resources\Fase\FaseNombreCollection;
use App\Http\Resources\Fase\FaseResource;
use App\Models\FaseModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class FaseController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new FaseModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = FaseModel::query();
        $query = $query->select('mas_fase.id', 'mas_fase.nombre', 'mas_fase.estado')->orderBy('mas_fase.nombre', 'asc')->get();

        return FaseCollection::make($query);
    }

    public function geFaseSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return FaseCollection::make($query);
    }

    public function getFaseEtapa($idEtapa)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($idEtapa) {
                return $model->where("id_etapa", $idEtapa)->orderBy("nombre", "asc")->get();
            }
        );

        return FaseCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FaseFormRequest $request)
    {
        return FaseResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return FaseResource::make($this->repository->find($id));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FaseFormRequest $request,  $id)
    {
        return FaseResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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

    /**
     *
     *
     */
    public function getFasesOrden()
    {
        $query = $this->repository->customQuery(
            function ($model) {
                return $model->whereNotNull('orden')->where('migrar', true)->orderBy("orden", "asc")->get();
            }
        );
        return FaseNombreCollection::make($query);
    }
}
