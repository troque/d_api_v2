<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InteresadoEntidadPermitidaFormRequest;
use App\Http\Resources\InteresadoEntidadPermitida\InteresadoEntidadPermitidaCollection;
use App\Http\Resources\InteresadoEntidadPermitida\InteresadoEntidadPermitidaResource;
use App\Models\InteresadoEntidadPermitidaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use App\Models\EntidadModel;
use Illuminate\Database\QueryException;
use App\Http\Resources\Entidad\EntidadCollection;

class InteresadoEntidadPermitidaController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new InteresadoEntidadPermitidaModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = InteresadoEntidadPermitidaModel::query();
        $query = $query->select('mas_entidad_permitida.id', 'mas_entidad_permitida.id_entidad', 'mas_entidad_permitida.estado')->orderBy('mas_entidad_permitida.created_at', 'desc')->get();

        foreach ($query as $r) {
            if ($r->id_entidad != "") {

                $nombreEntidad = $this->getNombreEntidad($r->id_entidad);
                if ($nombreEntidad[0] != null) {
                    $r->nombre_entidad = $nombreEntidad[0]["nombre"];
                }
            }
        }


        return InteresadoEntidadPermitidaCollection::make($query);
    }


    public function indexPaginate($paginaActual, $porPagina)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($porPagina, $paginaActual) {
                return InteresadoEntidadPermitidaModel::paginate($porPagina, ['*'], 'ciudades', $paginaActual);
            }
        );

        foreach ($query as $r) {
            if ($r->id_entidad != "") {

                $nombreEntidad = $this->getNombreEntidad($r->id_entidad);
                if ($nombreEntidad[0] != null) {
                    $r->nombre_entidad = $nombreEntidad[0]["nombre"];
                }
            }
        }

        return  InteresadoEntidadPermitidaCollection::make($query);
    }

    public function getNombreEntidad($idEntidad)
    {


        $query = EntidadModel::query();
        $query = $query->where("identidad", $idEntidad)->select('entidad.nombre')->orderBy('entidad.nombre', 'asc')->get();

        return EntidadCollection::make($query);
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\InteresadoEntidadPermitidaFormRequest  $request
     * @return App\Http\Resources\InteresadoEntidadPermitida\InteresadoEntidadPermitidaResource
     */
    public function store(InteresadoEntidadPermitidaFormRequest $request): InteresadoEntidadPermitidaResource

    {

        try {

            $datosRequest = $request->validated()["data"]["attributes"];
            $datosRequest['estado'] = 1;
            return InteresadoEntidadPermitidaResource::make($this->repository->create($datosRequest));
        } catch (QueryException  $e) {

            $error['estado'] = false;
            $error['error'] = $e->getMessage();
            return json_encode($error);
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
        return InteresadoEntidadPermitidaResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InteresadoEntidadPermitidaFormRequest $request,  $id)
    {

        try {
            // error_log($id);
            $datosRequest = $request->validated()["data"]["attributes"];
            // error_log(json_encode($datosRequest));
            return InteresadoEntidadPermitidaResource::make($this->repository->update($datosRequest, $id));
        } catch (QueryException  $e) {

            $error['estado'] = false;
            $error['error'] = $e->getMessage();
            return json_encode($error);
        }
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
