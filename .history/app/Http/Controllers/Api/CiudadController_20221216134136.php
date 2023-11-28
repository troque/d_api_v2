<?php

namespace App\Http\Controllers\Api;

use Adldap\Query\Paginator;
use App\Http\Controllers\Controller;
use App\Http\Requests\CiudadFormRequest;
use App\Http\Resources\Ciudad\CiudadCollection;
use App\Http\Resources\Ciudad\CiudadResource;
use App\Models\CiudadModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CiudadController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new CiudadModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        //return CiudadCollection::make($this->repository->paginate($request->limit ?? 20));

        $estado = true;
        $query = $this->repository->customQuery(
            function ($model) use ($estado) {
                return $model->where('estado', $estado)->orderBy("nombre", "asc")->get();
            }
        );
        return CiudadCollection::make($query);
    }

    public function indexPaginate($paginaActual, $porPagina)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($porPagina, $paginaActual) {
                return CiudadModel::orderBy('nombre', 'asc')->paginate($porPagina, ['*'], 'ciudades', $paginaActual);
            }
        );
        return  CiudadCollection::make($query);
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\CiudadFormRequest  $request
     * @return App\Http\Resources\Ciudad\CiudadResource
     */
    public function store(CiudadFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        $nombre = $datosRequest["nombre"];
        $consulta = DB::select("
                select * from mas_ciudad
                where Translate(upper(nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu') = Translate(upper('" . $nombre . "'),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')
            ");
        DB::connection()->commit();

        if (count($consulta) >= 1) {

            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'Ya existe un registro con esta ciudad';
            return json_encode($error);
        } else if (count($consulta) == 0) {

            try {

                $datosRequest = $request->validated()["data"]["attributes"];

                return CiudadResource::make($this->repository->create($request->validated()["data"]["attributes"]));
            } catch (\Exception $e) {

                if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                    $error['estado'] = false;
                    $error['error'] = 'Ya existe un registro con esta ciudad.';

                    return json_encode($error);
                }
            }
        } else {

            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'TENEMOS UN ERROR';
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
        //return CiudadModel::findOrFail($id)->load("departamento");
        return CiudadResource::make($this->repository->find($id)->load("departamento"));
    }

    public function getCiudadesPorDepartamento(CiudadFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        $estado = true;
        $query = $this->repository->customQuery(function ($model) use ($datosRequest, $estado) {
            return $model->where('id_departamento', $datosRequest["id_departamento"])->where('estado', $estado)->orderBy('MAS_CIUDAD.NOMBRE', 'asc')->get();
        });


        return CiudadCollection::make($query);
    }

    public function getCiudadSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return CiudadCollection::make($query);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CiudadFormRequest $request,  $id)
    {
        return CiudadResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
