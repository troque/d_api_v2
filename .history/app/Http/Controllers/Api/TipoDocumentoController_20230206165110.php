<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoDocumentoFormRequest;
use App\Http\Resources\TipoDocumento\TipoDocumentoCollection;
use App\Http\Resources\TipoDocumento\TipoDocumentoResource;
use App\Models\TipoDocumentoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoDocumentoController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoDocumentoModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = TipoDocumentoModel::query();
        $query = $query->select('mas_tipo_documento.id', 'mas_tipo_documento.nombre', 'mas_tipo_documento.estado')->orderBy('mas_tipo_documento.nombre', 'asc')->get();

        return TipoDocumentoCollection::make($query);
        //return CiudadCollection::make($query);

        //return TipoDocumentoListResource::collection($this->repository->orderBy($request->get('nombre')));
        //return TipoDocumentoCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function indexPaginate($paginaActual, $porPagina)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($porPagina, $paginaActual) {
                return TipoDocumentoModel::orderBy('nombre', 'asc')->paginate($porPagina, ['*'], 'ciudades', $paginaActual);
            }
        );
        return  TipoDocumentoCollection::make($query);
    }

    public function geTipoDocumentoSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return TipoDocumentoCollection::make($query);
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\TipoDocumentoFormRequest  $request
     * @return App\Http\Resources\TipoDocumento\TipoDocumentoResource
     */
    public function store(TipoDocumentoFormRequest $request)
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
                from mas_tipo_documento
                where Translate(upper(nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu') = Translate(upper('" . $nombre . "'),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')
            ");
        DB::connection()->commit();

        if (count($consulta) >= 1) {

            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'Ya existe un registro con este Documento';
            return json_encode($error);
        } else if (count($consulta) == 0) {

            try {

                $datosRequest = $request->validated()["data"]["attributes"];

                return TipoDocumentoResource::make($this->repository->create($request->validated()["data"]["attributes"]));
            } catch (\Exception $e) {

                if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                    $error['estado'] = false;
                    $error['error'] = 'Ya existe un registro con este Documento.';

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

        return TipoDocumentoResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoDocumentoFormRequest $request,  $id)
    {
        return TipoDocumentoResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
