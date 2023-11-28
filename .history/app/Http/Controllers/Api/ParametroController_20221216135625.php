<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParametroFormRequest;
use App\Http\Resources\Parametro\ParametroCollection;
use App\Http\Resources\Parametro\ParametroResource;
use App\Repositories\RepositoryGeneric;
use App\Models\ParametroModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParametroController extends Controller
{
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ParametroModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = ParametroModel::orderBy('modulo')->orderBy("nombre")->get();
        return ParametroCollection::make($query);
    }

    public function indexPaginate($paginaActual, $porPagina)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($porPagina, $paginaActual) {
                return ParametroModel::orderBy('nombre', 'asc')->paginate($porPagina, ['*'], 'ciudades', $paginaActual);
            }
        );
        return  ParametroCollection::make($query);
    }

    public function getParameterByName(ParametroFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        $query = $this->repository->customQuery(function ($model) use ($datosRequest) {
            return $model->whereIn('nombre', explode('|', $datosRequest["nombre"]))
                ->where('estado', true)
                ->get();
        });

        return ParametroCollection::make($query);
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\ParametroFormRequest  $request
     * @return App\Http\Resources\Parametro\ParametroResource
     */
    // public function store(ParametroFormRequest $request)
    // {
    //     return ParametroResource::make($this->repository->create($request->validated()));
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ParametroResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ParametroFormRequest $request,  $id)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        $nombre_campo = $datosRequest["nombre_campo"];
        $consulta = DB::select("
                select * from mas_parametro_campos
                where Translate(upper(nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu') = Translate(upper('" . $nombre_campo . "'),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')
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

                return ParametroResource::make($this->repository->create($request->validated()["data"]["attributes"]));
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

        $parametro = $this->repository->find($id);
        if ($parametro == null)
            return response()->json(['message' => 'Not Found!'], 404);
        else {

            $requestModel = $request->validated()["data"]["attributes"];

            if ($parametro['nombre'] == 'maximo_caracteres_textarea') {

                $queryMinimo = $this->repository->customQuery(function ($model) use ($requestModel, $id) {
                    return $model->where('nombre', 'minimo_caracteres_textarea')->first();
                });

                if ((int)$queryMinimo["valor"] > (int)$requestModel["valor"]) {
                    $error['estado'] = false;
                    "messageDetail";
                    $error['error'] = 'No número máximo no puede ser menor al número mínimo';
                    return json_encode($error);
                }
            } else if ($parametro['nombre'] == 'minimo_caracteres_textarea') {

                $queryMaximo = $this->repository->customQuery(function ($model) use ($requestModel, $id) {
                    return $model->where('nombre', 'maximo_caracteres_textarea')->first();
                });

                if ((int)$queryMaximo["valor"] < (int)$requestModel["valor"]) {
                    $error['estado'] = false;
                    "messageDetail";
                    $error['error'] = 'No número mínimo no puede ser mayor al número máximo';
                    return json_encode($error);
                }
            }


            $requestModel['modulo'] = $parametro['modulo'];
            $requestModel['nombre'] = $parametro['nombre'];
            return ParametroResource::make($this->repository->update($requestModel, $id));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     $this->repository->delete($id);
    //     return response()->noContent();
    // }
}
