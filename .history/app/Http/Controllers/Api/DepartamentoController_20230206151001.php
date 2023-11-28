<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartamentoFormRequest;
use App\Http\Resources\Departamento\DepartamentoCollection;
use App\Http\Resources\Departamento\DepartamentoResource;
use App\Models\DepartamentoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\UtilidadesTrait;
use App\Http\Utilidades\Constants;
use Illuminate\Support\Facades\DB;
use Error;

class DepartamentoController extends Controller
{
    use UtilidadesTrait;
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new DepartamentoModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = DepartamentoModel::query();
        $query = $query->select('mas_departamento.*')->where('estado', true)->orderBy('mas_departamento.nombre', 'asc')->get();

        return DepartamentoCollection::make($query);
        //return CiudadCollection::make($query);

        //return DepartamentoListResource::collection($this->repository->orderBy($request->get('nombre')));
        //return DepartamentoCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function indexPaginate($paginaActual, $porPagina)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($porPagina, $paginaActual) {
                return DepartamentoModel::orderBy('nombre', 'asc')->paginate($porPagina, ['*'], 'departamento', $paginaActual);
            }
        );
        return  DepartamentoCollection::make($query);
    }

    public function getDepartamentosSinEstado()
    {

        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }

        );

        return DepartamentoCollection::make($query);
    }


    /***
     *
     */
    public function getDepartamentosActivos()
    {

        $query = $this->repository->customQuery(
            function ($model) {
                return $model->where("estado", Constants::ESTADOS['activo'])->orderBy("nombre", "asc")->get();
            }
        );

        return DepartamentoCollection::make($query);
    }





    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\DepartamentoFormRequest  $request
     * @return App\Http\Resources\Departamento\DepartamentoResource
     */
    public function store(DepartamentoFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        $nombre = $datosRequest["nombre"];
        $consulta = DB::select("
                select
                    id,
                    nombre,
                    codigo_dane,
                    created_user,
                    updated_user,
                    deleted_user,
                    created_at,
                    updated_at,
                    deleted_at,
                    estado
                from
                    mas_departamento
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
                $queryRadicado = $this->repository->customQuery(function ($model) use ($datosRequest) {
                    return $model->where('codigo_dane', $datosRequest['codigo_dane'])->get();
                });

                if (!empty($queryRadicado[0])) {

                    $error['estado'] = false;
                    "messageDetail";
                    $error['error'] = 'El código DANE ya esta en uso, digite otro';
                    return json_encode($error);
                } else {
                    //return DepartamentoResource::make($this->repository->create($request->validated()));
                    return DepartamentoResource::make($this->repository->create($datosRequest));
                }
            } catch (\Exception $e) {

                if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                    $error['estado'] = false;
                    $error['error'] = 'Ya existe un registro con este departamento.';

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
        return DepartamentoResource::make($this->repository->find($id)->load("ciudades"));
    }


    public function departamentoPorId($id)
    {
        $query = DepartamentoModel::query();
        $query = $query->where('mas_departamento.id', $id)->select('mas_departamento.id', 'mas_departamento.nombre', 'mas_departamento.codigo_dane', 'mas_departamento.estado')->get();

        return DepartamentoCollection::make($query);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DepartamentoFormRequest $request,  $id)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        $queryRadicado = $this->repository->customQuery(function ($model) use ($datosRequest, $id) {
            return $model->where('codigo_dane', $datosRequest['codigo_dane'])
                ->where("id", "<>", $id)->get();
        });


        if (!empty($queryRadicado[0])) {
            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'El código DANE ya esta en uso, digite otro';
            return json_encode($error);
        }

        return DepartamentoResource::make($this->repository->update($datosRequest, $id));
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
