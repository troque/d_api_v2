<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoConductaFormRequest;
use App\Http\Resources\TipoConducta\TipoConductaCollection;
use App\Http\Resources\TipoConducta\TipoConductaResource;
use App\Models\TipoConductaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TipoConductaController extends Controller

{   private $repository;
    public function __construct(RepositoryGeneric $repository) {
        $this->repository = $repository;
        $this->repository->setModel(new TipoConductaModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = TipoConductaModel::query();
        $query = $query->select('mas_tipo_conducta.*')->orderBy('mas_tipo_conducta.conducta_nombre', 'asc')->get();

        return TipoConductaCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\TipoConductaFormRequest  $request
     * @return \Illuminate\Http\Resources\TipoConductaResource
     */
    public function store(TipoConductaFormRequest $request): TipoConductaResource
    {
        return TipoConductaResource::make($this->repository->create($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TipoConductaResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoConductaFormRequest $request, $id)
    {
        return TipoConductaResource::make($this->repository->update($request->validated(),$id));
    }

}
