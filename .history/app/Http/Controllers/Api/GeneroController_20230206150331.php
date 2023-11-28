<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneroFormRequest;
use App\Http\Resources\Genero\GeneroCollection;
use App\Http\Resources\Genero\GeneroResource;
use App\Http\Resources\Genero\GeneroListResource;
use App\Models\GeneroModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class GeneroController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new GeneroModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = GeneroModel::query();
        $query = $query->select('mas_genero.id', 'mas_genero.nombre', 'mas_genero.estado')->orderBy('mas_genero.nombre', 'asc')->get();

        return GeneroCollection::make($query);
        //return CiudadCollection::make($query);

        //return GeneroListResource::collection($this->repository->orderBy($request->get('nombre')));
        //return GeneroCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function geGeneroSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return GeneroCollection::make($query);
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\GeneroFormRequest  $request
     * @return App\Http\Resources\Genero\GeneroResource
     */
    public function store(GeneroFormRequest $request)
    {
        // error_log("insert");
        try {
            return GeneroResource::make($this->repository->create($request->validated()["data"]["attributes"]));
        } catch (\Exception $e) {

            if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                $error['estado'] = false;
                $error['error'] = 'Ya existe un registro con este genero.';

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
        return GeneroResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GeneroFormRequest $request,  $id)
    {
        return GeneroResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
