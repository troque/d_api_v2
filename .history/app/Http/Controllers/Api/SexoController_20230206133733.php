<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SexoFormRequest;
use App\Http\Resources\Sexo\SexoCollection;
use App\Http\Resources\Sexo\SexoResource;
use App\Models\SexoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class SexoController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new SexoModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = SexoModel::query();
        $query = $query->select('mas_sexo.id, mas_sexo.nombre, mas_sexo.estado')->orderBy('mas_sexo.nombre', 'asc')->get();

        return SexoCollection::make($query);
        //return CiudadCollection::make($query);

        //return SexoListResource::collection($this->repository->orderBy($request->get('nombre')));
        //return SexoCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     *
     */
    public function geSexoSinEstado()
    {
        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return SexoCollection::make($query);
    }
    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\SexoFormRequest  $request
     * @return App\Http\Resources\Sexo\SexoResource
     */
    public function store(SexoFormRequest $request)
    { {
            // error_log("insert");
            try {
                return SexoResource::make($this->repository->create($request->validated()["data"]["attributes"]));
            } catch (\Exception $e) {

                if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                    $error['estado'] = false;
                    $error['error'] = 'Ya existe un registro con este sexo.';

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
        return SexoResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SexoFormRequest $request,  $id)
    {
        return SexoResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
