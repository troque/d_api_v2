<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoFirmaFormRequest;
use App\Http\Resources\TipoFirma\TipoFirmaCollection;
use App\Http\Resources\TipoFirma\TipoFirmaResource;
use App\Http\Utilidades\Constants;
use App\Models\TipoFirmaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class MasTipoFirmaController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoFirmaModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TipoFirmaCollection::make($this->repository->paginate($request->limit ?? 100));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\TipoConductaFormRequest  $request
     * @return \Illuminate\Http\Resources\TipoConductaResource
     */
    public function store(TipoFirmaFormRequest $request)
    {
        try {
            return TipoFirmaResource::make($this->repository->create($request->validated()["data"]["attributes"]));
        } catch (\Exception $e) {

            if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                $error['estado'] = false;
                $error['error'] = 'YA EXISTE UN REGISTRO CON ESE NOMBRE';

                return json_encode($error);
            }
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
        error_log("p1");
        return TipoFirmaResource::make($this->repository->find($id));
        error_log("p2");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoFirmaFormRequest $request, $id)
    {
        return TipoFirmaResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
