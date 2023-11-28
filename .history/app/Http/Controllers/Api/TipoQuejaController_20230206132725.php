<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoQuejaFormRequest;
use App\Http\Resources\TipoQueja\TipoQuejaCollection;
use App\Http\Resources\TipoQueja\TipoQuejaResource;
use App\Http\Utilidades\Constants;
use App\Models\ClasificacionRadicadoModel;
use App\Models\TipoQuejaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoQuejaController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoQuejaModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TipoQuejaCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function geTipoQuejaSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return TipoQuejaCollection::make($query);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TipoQuejaFormRequest $request)
    {
        return TipoQuejaResource::make($this->repository->create($request->validated()["data"]["attributes"]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TipoQuejaResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoQuejaFormRequest $request, $id)
    {
        return TipoQuejaResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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

    /**
     *
     */
    public function getTiposQuejaHabilitados($id_proceso_disciplinario)
    {

        $query = TipoQuejaModel::query();
        $mas_dependencia_configuracion = DB::select("select id_dependencia_origen from mas_dependencia_configuracion where id_dependencia_origen = " . auth()->user()->id_dependencia . " and id_dependencia_acceso = 9");

        $expediente = ClasificacionRadicadoModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)
            ->where('estado', Constants::ESTADOS['activo'])
            ->where('id_tipo_expediente', Constants::TIPO_EXPEDIENTE['queja'])
            ->get();

        if ($mas_dependencia_configuracion != null && count($expediente) < 0) {

            if (count($expediente) > 0) {
                $query = $query->select('mas_tipo_queja.id, mas_tipo_queja.nombre, mas_tipo_queja.estado')->where('id', '<>', $expediente[0]->id_tipo_queja)->where('id', '<>', Constants::TIPO_QUEJA['externa'])->get();
            } else {
                $query = $query->select('mas_tipo_queja.id, mas_tipo_queja.nombre, mas_tipo_queja.estado')->where('id', '=', Constants::TIPO_QUEJA['interna'])->get();
            }
            return TipoQuejaCollection::make($query);
        } elseif ($mas_dependencia_configuracion == null) {

            if (count($expediente) > 0) {
                $query = $query->select('mas_tipo_queja.id, mas_tipo_queja.nombre, mas_tipo_queja.estado')->where('id', '<>', $expediente[0]->id_tipo_queja)->where('id', '<>', Constants::TIPO_QUEJA['interna'])->get();
            } else {
                $query = $query->select('mas_tipo_queja.id, mas_tipo_queja.nombre, mas_tipo_queja.estado')->where('id', '=', Constants::TIPO_QUEJA['externa'])->get();
            }
            return TipoQuejaCollection::make($query);
        }


        $query = $query->select('mas_tipo_queja.id, mas_tipo_queja.nombre, mas_tipo_queja.estado')->orderBy('mas_tipo_queja.nombre', 'asc')->get();

        return TipoQuejaCollection::make($query);
    }

    public function getTiposQueja()
    {
        $query = TipoQuejaModel::query();
        $query = $query->select('mas_tipo_queja.*')->orderBy('mas_tipo_queja.nombre', 'asc')->get();
        return TipoQuejaCollection::make($query);
    }
}
