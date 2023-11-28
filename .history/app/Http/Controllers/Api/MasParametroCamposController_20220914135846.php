<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasParametroCamposModel;
use App\Http\Requests\ParametroCamposFormRequest;
use App\Http\Resources\ParametroCampos\ParametroCamposCollection;
use App\Http\Resources\ParametroCampos\ParametroCamposResource;
use App\Repositories\RepositoryGeneric;
use Illuminate\Support\Facades\DB;

class MasParametroCamposController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new MasParametroCamposModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ParametroCamposCollection::make($this->repository->paginate($request->limit ?? 100));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ParametroCamposFormRequest $request)
    {
        return ParametroCamposResource::make($this->repository->create($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ParametroCamposResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ParametroCamposFormRequest $request, $id)
    {
        return ParametroCamposResource::make($this->repository->update($request->validated(), $id));
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