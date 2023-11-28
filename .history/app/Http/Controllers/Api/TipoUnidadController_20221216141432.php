<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoUnidadModel;
use App\Repositories\RepositoryGeneric;
use App\Http\Resources\TipoUnidad\TipoUnidadCollection;
use App\Http\Resources\TipoUnidad\TipoUnidadResource;
use App\Http\Requests\TipoUnidadFormRequest;

class TipoUnidadController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoUnidadModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TipoUnidadCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TipoUnidadFormRequest $request)
    {
        return TipoUnidadResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TipoUnidadResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoUnidadFormRequest $request, $id)
    {
        return TipoUnidadResource::make($this->repository->update($request->validated(), $id));
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
}
