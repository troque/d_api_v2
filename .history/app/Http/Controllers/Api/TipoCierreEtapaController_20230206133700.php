<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoCierreEtapaFormRequest;
use App\Http\Resources\TipoConducta\TipoCierreEtapaResource;
use App\Http\Resources\TipoConducta\TipoConductaCollection;
use App\Models\TipoCierreEtapaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TipoCierreEtapaController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoCierreEtapaModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = TipoCierreEtapaModel::query();
        $query = $query->select('mas_tipo_cierre_etapa.id, mas_tipo_cierre_etapa.nombre, mas_tipo_cierre_etapa.estado')->orderBy('mas_tipo_conducta.nombre', 'asc')->get();

        return TipoConductaCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\TipoConductaFormRequest  $request
     * @return \Illuminate\Http\Resources\TipoConductaResource
     */
    public function store(TipoCierreEtapaFormRequest $request)
    {
        // error_log("insert");
        try {
            return TipoCierreEtapaResource::make($this->repository->create($request->validated()["data"]["attributes"]));
        } catch (\Exception $e) {

            if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                $error['estado'] = false;
                $error['error'] = 'Ya existe un registro con ese nombre.';

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
        return TipoCierreEtapaResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoCierreEtapaFormRequest $request, $id)
    {
        return TipoCierreEtapaResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
