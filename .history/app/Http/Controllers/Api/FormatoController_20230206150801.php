<?php

namespace App\Http\Controllers\Api;

use Adldap\Utilities;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormatoFormRequest;
use App\Http\Resources\Formato\FormatoCollection;
use App\Http\Resources\Formato\FormatoResource;
use App\Http\Utilidades\Constants;
use App\Http\Utilidades\Utilidades;
use App\Models\FormatoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;

class FormatoController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new FormatoModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $query = FormatoModel::query();
        $query = $query->select('mas_formato.id', 'mas_formato.nombre', 'mas_formato.estado')->where('estado', Constants::ESTADOS['activo'])->orderBy('mas_formato.nombre', 'asc')->get();

        return FormatoCollection::make($query);
    }

    public function indexPaginate($paginaActual, $porPagina)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($porPagina, $paginaActual) {
                return FormatoModel::orderBy('nombre', 'asc')->paginate($porPagina, ['*'], 'ciudades', $paginaActual);
            }
        );
        return  FormatoCollection::make($query);
    }

    public function geFormatoSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return FormatoCollection::make($query);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FormatoFormRequest $request)
    {

        try {
            return FormatoResource::make($this->repository->create($request->validated()["data"]["attributes"]));
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
        return FormatoResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FormatoFormRequest $request, $id)
    {
        return FormatoResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
