<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\RepositoryGeneric;
use App\Models\TipoExpedienteMensajesModel;
use App\Http\Resources\TipoExpedienteMensajes\TipoExpedienteMensajesCollection;
use App\Http\Resources\TipoExpedienteMensajes\TipoExpedienteMensajesResource;
use App\Http\Requests\TipoExpedienteMensajesFormRequest;

class TipoExpedienteMensajesController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoExpedienteMensajesModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TipoExpedienteMensajesCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TipoExpedienteMensajesFormRequest $request)
    {
        $registro = $request->validated()["data"]["attributes"];

        if(isset($registro['id_sub_tipo_expediente'])){
            $query = TipoExpedienteMensajesModel::where('id_tipo_expediente', $registro['id_tipo_expediente'])->where('id_sub_tipo_expediente', $registro['id_sub_tipo_expediente'])->get();
        }
        else{
            $query = TipoExpedienteMensajesModel::where('id_tipo_expediente', $registro['id_tipo_expediente'])->get();
        }

        if(count($query) > 0){
            $error['estado'] = false;
            if(isset($registro['id_sub_tipo_expediente'])){
                $error['error'] = 'YA HAY UN REGISTRO DEL TIPO DE EXPEDIENTE: ' . $registro['tipo_expediente_nombre'] . ' Y SUB-EXPEDIENTE: ' . $registro['sub_tipo_expediente_nombre'] . ', SELECCIONADO';
            }
            else{
                $error['error'] = 'YA HAY UN REGISTRO DEL TIPO DE EXPEDIENTE: ' . $registro['tipo_expediente_nombre'] . ', SELECCIONADO';
            }
            return json_encode($error);
        }

        return TipoExpedienteMensajesResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TipoExpedienteMensajesResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoExpedienteMensajesFormRequest $request, $id)
    {
        $registro = $request->validated()["data"]["attributes"];

        if(isset($registro['id_sub_tipo_expediente'])){
            $query = TipoExpedienteMensajesModel::where('id_tipo_expediente', $registro['id_tipo_expediente'])->where('id_sub_tipo_expediente', $registro['id_sub_tipo_expediente'])->get();
        }
        else{
            $query = TipoExpedienteMensajesModel::where('id_tipo_expediente', $registro['id_tipo_expediente'])->get();
        }

        if(count($query) > 0){
            if($query[0]->id."" !== $id){
                $error['estado'] = false;
                if(isset($registro['id_sub_tipo_expediente'])){
                    $error['error'] = 'YA HAY UN REGISTRO DEL TIPO DE EXPEDIENTE: ' . $registro['tipo_expediente_nombre'] . ' Y SUB-EXPEDIENTE: ' . $registro['sub_tipo_expediente_nombre'] . ', SELECCIONADO';
                }
                else{
                    $error['error'] = 'YA HAY UN REGISTRO DEL TIPO DE EXPEDIENTE: ' . $registro['tipo_expediente_nombre'] . ', SELECCIONADO';
                }
                return json_encode($error);
            }
        }
        
        return TipoExpedienteMensajesResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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

    public function obtenerInformacionTipoExpediente($idTipoExpediente, $idSubTipoExpediente)
    {

        $query = $this->repository->customQuery(function ($model) use ($idTipoExpediente, $idSubTipoExpediente) {
            return $model->where('id_tipo_expediente', $idTipoExpediente)
                ->where('id_sub_tipo_expediente', $idSubTipoExpediente)
                ->get();
        });

        return TipoExpedienteMensajesCollection::make($query);
    }
}
