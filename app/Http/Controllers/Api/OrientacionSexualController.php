<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrientacionSexualFormRequest;
use App\Http\Resources\OrientacionSexual\OrientacionSexualCollection;
use App\Http\Resources\OrientacionSexual\OrientacionSexualResource;
use App\Models\OrientacionSexualModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class OrientacionSexualController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new OrientacionSexualModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = OrientacionSexualModel::query();
        $query = $query->select('mas_orientacion_sexual.id', 'mas_orientacion_sexual.nombre', 'mas_orientacion_sexual.estado')->orderBy('mas_orientacion_sexual.nombre', 'asc')->get();

        return OrientacionSexualCollection::make($query);
        //return CiudadCollection::make($query);

        //return OrientacionSexualListResource::collection($this->repository->orderBy($request->get('nombre')));
        //return OrientacionSexualCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function getOrientacionSexual($estado)
    {


        $query = $this->repository->customQuery(
            function ($model) use ($estado) {
                return $model->where("estado", $estado)->orderBy("nombre", "asc")->get();
            }
        );

        return OrientacionSexualCollection::make($query);
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\OrientacionSexualFormRequest  $request
     * @return App\Http\Resources\OrientacionSexual\OrientacionSexualResource
     */
    public function store(OrientacionSexualFormRequest $request)
    { {
            // error_log("insert");
            try {
                return OrientacionSexualResource::make($this->repository->create($request->validated()["data"]["attributes"]));
            } catch (\Exception $e) {

                if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                    $error['estado'] = false;
                    $error['error'] = 'Ya existe un registro con esta orientacion.';

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
        return OrientacionSexualResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrientacionSexualFormRequest $request,  $id)
    {
        return OrientacionSexualResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
