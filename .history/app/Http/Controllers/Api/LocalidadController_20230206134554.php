<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocalidadFormRequest;
use App\Http\Resources\Localidad\LocalidadCollection;
use App\Http\Resources\Localidad\LocalidadResource;
use App\Models\LocalidadModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocalidadController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new LocalidadModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = LocalidadModel::query();
        $query = $query->select('mas_localidad.id, mas_localidad.nombre, mas_localidad.estado')->orderBy('mas_localidad.nombre', 'asc')->get();

        return LocalidadCollection::make($query);
        //return CiudadCollection::make($query);

        //return LocalidadListResource::collection($this->repository->orderBy($request->get('nombre')));
        //return LocalidadCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function indexPaginate($paginaActual, $porPagina)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($porPagina, $paginaActual) {
                return LocalidadModel::orderBy('nombre', 'asc')->paginate($porPagina, ['*'], 'ciudades', $paginaActual);
            }
        );
        return  LocalidadCollection::make($query);
    }

    public function geLocalidadSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return LocalidadCollection::make($query);
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\LocalidadFormRequest  $request
     * @return App\Http\Resources\Localidad\LocalidadResource
     */
    public function store(LocalidadFormRequest $request)

    {

        $datosRequest = $request->validated()["data"]["attributes"];
        $nombre = $datosRequest["nombre"];
        $consulta = DB::select("
                select
                    id,
                    nombre,
                    created_user,
                    updated_user,
                    deleted_user,
                    created_at,
                    updated_at,
                    deleted_at,
                    estado
                from mas_localidad
                where Translate(upper(nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu') = Translate(upper('" . $nombre . "'),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')
            ");
        DB::connection()->commit();

        if (count($consulta) >= 1) {

            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'Ya existe un registro con este nombre';
            return json_encode($error);
        } else if (count($consulta) == 0) {

            try {

                $datosRequest = $request->validated()["data"]["attributes"];

                return LocalidadResource::make($this->repository->create($request->validated()["data"]["attributes"]));
            } catch (\Exception $e) {

                if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                    $error['estado'] = false;
                    $error['error'] = 'Ya existe un registro con este Localidad.';

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
        return LocalidadResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LocalidadFormRequest $request,  $id)
    {
        return LocalidadResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
