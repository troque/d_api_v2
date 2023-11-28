<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoConductaFormRequest;
use App\Http\Resources\TipoConducta\TipoConductaCollection;
use App\Http\Resources\TipoConducta\TipoConductaResource;
use App\Http\Utilidades\Constants;
use App\Models\EvaluacionModel;
use App\Models\TipoCerrarEtapaModel;
use App\Models\TipoConductaModel;
use App\Models\TipoLogModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TipoCerrarEtapaController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository) {
        $this->repository = $repository;
        $this->repository->setModel(new TipoCerrarEtapaModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = TipoCerrarEtapaModel::query();
        $query = $query->select('mas_tipo_cierre_etapa.*')->orderBy('mas_tipo_conducta.nombre', 'asc')->get();

        return TipoConductaCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\TipoConductaFormRequest  $request
     * @return \Illuminate\Http\Resources\TipoConductaResource
     */
    public function store(TipoConductaFormRequest $request)
    {
        error_log("insert");
         try{
            return TipoConductaResource::make($this->repository->create($request->validated()));


        } catch (\Exception $e) {

            if(strpos($e->getMessage(), 'ORA-00001') !== false)  {

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
        return TipoConductaResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoConductaFormRequest $request, $id)
    {
        return TipoConductaResource::make($this->repository->update($request->validated(),$id));
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


    /**
     *
     */
    public function getTiposConductasHabilitadas($id_proceso_disciplinario){

        $query = TipoConductaModel::query();

        $evaluacion = EvaluacionModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)
        ->where('estado', Constants::ESTADOS['activo'])
        ->get();

        if(count($evaluacion)>0){
            $query = $query->select('mas_tipo_conducta.*')->where('id', '<>', $evaluacion[0]->tipo_conducta)->orderBy('mas_tipo_conducta.nombre', 'asc')->get();
            return TipoConductaCollection::make($query);
        }

        $query = $query->select('mas_tipo_conducta.*')->orderBy('mas_tipo_conducta.nombre', 'asc')->get();
        return TipoConductaCollection::make($query);
    }
}
