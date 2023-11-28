<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VigenciaFormRequest;
use App\Http\Resources\Vigencia\VigenciaCollection;
use App\Http\Resources\Vigencia\VigenciaResource;
use App\Http\Resources\Vigencia\VigenciaListResource;
use App\Models\VigenciaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class VigenciaController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new VigenciaModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $estado = $request->query('estado');
        if ($estado != null)
            $query = VigenciaModel::where("estado", $estado)->orderBy("vigencia", 'desc')->get();
        else
            $query = VigenciaModel::orderBy("vigencia", 'desc')->get();

        return VigenciaCollection::make($query);
        //return VigenciaCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function indexPaginate(Request $request, $paginaActual, $porPagina)
    {
        $estado = $request->query('estado');
        $query = $this->repository->customQuery(
            function ($model) use ($porPagina, $paginaActual, $estado) {
                if ($estado != null)
                    return VigenciaModel::where("estado", $estado)->orderBy('vigencia', 'asc')->paginate($porPagina, ['*'], 'vigencias', $paginaActual);
                else
                    return VigenciaModel::orderBy('vigencia', 'asc')->paginate($porPagina, ['*'], 'vigencias', $paginaActual);
            }
        );
        return  VigenciaCollection::make($query);
    }

    public function geVigenciaSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("vigencia", "desc")->get();
            }
        );

        return VigenciaCollection::make($query);
    }


    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\VigenciaFormRequest  $request
     * @return App\Http\Resources\Vigencia\VigenciaResource
     */
    public function store(VigenciaFormRequest $request)
    { {
            // error_log("insert");
            try {
                return VigenciaResource::make($this->repository->create($request->validated()));
            } catch (\Exception $e) {

                if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                    $error['estado'] = false;
                    $error['error'] = 'Ya existe un registro con esta vigencia.';

                    return json_encode($error);
                }
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
        return VigenciaResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VigenciaFormRequest $request,  $id)
    {
        return VigenciaResource::make($this->repository->update($request->validated(), $id));
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
