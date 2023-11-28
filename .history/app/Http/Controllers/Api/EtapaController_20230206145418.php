<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EtapaFormRequest;
use App\Http\Resources\Etapa\EtapaCollection;
use App\Http\Resources\Etapa\EtapaResource;
use App\Models\EtapaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class EtapaController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new EtapaModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = EtapaModel::query();
        $query = $query
            ->select('mas_etapa.id', 'mas_etapa.nombre, 'mas_etapa.estado')
            ->where('mas_etapa.estado', 1)
            ->orderBy('mas_etapa.id', 'asc')->get();

        return EtapaCollection::make($query);
    }

    /**
     *
     */
    public function geEtapaSinEstado()
    {
        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return EtapaCollection::make($query);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EtapaFormRequest $request)
    {
        return EtapaResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return EtapaResource::make($this->repository->find($id));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EtapaFormRequest $request, $id)
    {
        return EtapaResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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


    public function geEtapasPoderPreferenteActivas()
    {
        $query = $this->repository->customQuery(
            function ($model) {
                return $model->where('estado_poder_preferente', 1)->orderBy("nombre", "asc")->get();
            }
        );

        return EtapaCollection::make($query);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEtapaNuevos()
    {
        $query = EtapaModel::query();
        $query = $query
            ->select('mas_etapa.id', 'mas_etapa.nombre', 'mas_etapa.estado')
            ->where('mas_etapa.estado', 1)
            ->whereNotIn('mas_etapa.id', [0, 1, 2, 9])
            ->orderBy('mas_etapa.nombre', 'asc')->get();

        return EtapaCollection::make($query);
    }
}
