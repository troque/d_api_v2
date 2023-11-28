<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoSujetoProcesalFormRequest;
use App\Http\Resources\TipoSujetoProcesal\TipoSujetoProcesalCollection;
use App\Http\Resources\TipoSujetoProcesal\TipoSujetoProcesalResource;
use App\Models\TipoSujetoProcesalModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoSujetoProcesalController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoSujetoProcesalModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = TipoSujetoProcesalModel::query();
        $query = $query->select('mas_tipo_sujeto_procesal.*')->orderBy('mas_tipo_sujeto_procesal.nombre', 'asc')->get();

        return TipoSujetoProcesalCollection::make($query);
        //return CiudadCollection::make($query);

        //return TipoSujetoProcesalListResource::collection($this->repository->orderBy($request->get('nombre')));
        //return TipoSujetoProcesalCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\TipoSujetoProcesalFormRequest  $request
     * @return App\Http\Resources\TipoSujetoProcesal\TipoSujetoProcesalResource
     */
    public function store(TipoSujetoProcesalFormRequest $request)
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
                from mas_tipo_sujeto_procesal
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

                return TipoSujetoProcesalResource::make($this->repository->create($datosRequest));
            } catch (\Exception $e) {

                if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                    $error['estado'] = false;
                    $error['error'] = 'Ya existe un registro con este nombre.';

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
        return TipoSujetoProcesalResource::make($this->repository->find($id));
    }

    public function sujetoPorcesalPorId($id)
    {
        $query = TipoSujetoProcesalModel::query();
        $query = $query->where('mas_tipo_sujeto_procesal.id', $id)->select('mas_tipo_sujeto_procesal.id, mas_tipo_sujeto_procesal.nombre, mas_tipo_sujeto_procesal.estado')->get();

        return TipoSujetoProcesalCollection::make($query);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoSujetoProcesalFormRequest $request,  $id)
    {

        return TipoSujetoProcesalResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
