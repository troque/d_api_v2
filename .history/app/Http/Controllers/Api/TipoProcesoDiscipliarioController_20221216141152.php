<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoDerechoPeticionFormRequest;
use App\Http\Resources\TipoDerechoPeticion\TipoDerechoPeticionCollection;
use App\Http\Resources\TipoDerechoPeticion\TipoDerechoPeticionResource;
use App\Http\Utilidades\Constants;
use App\Models\ClasificacionRadicadoModel;
use App\Models\TipoDerechoPeticionModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoDerechoPeticionController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TipoDerechoPeticionModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TipoDerechoPeticionCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function geTipoDerechoPeticionSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return TipoDerechoPeticionCollection::make($query);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TipoDerechoPeticionFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        $nombre = $datosRequest["nombre"];
        $consulta = DB::select("
                select * from mas_tipo_derecho_peticion
                where Translate(upper(nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu') = Translate(upper('" . $nombre . "'),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')
            ");
        DB::connection()->commit();

        if (count($consulta) >= 1) {

            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'Ya existe un registro con esta peticion';
            return json_encode($error);
        } else if (count($consulta) == 0) {

            try {

                $datosRequest = $request->validated()["data"]["attributes"];

                return TipoDerechoPeticionResource::make($this->repository->create($request->validated()["data"]["attributes"]));
            } catch (\Exception $e) {

                if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                    $error['estado'] = false;
                    $error['error'] = 'Ya existe un registro con esta peticion.';

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
        return TipoDerechoPeticionResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoDerechoPeticionFormRequest $request,  $id)
    {
        return TipoDerechoPeticionResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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

    public function getTiposDerechoPeticionHabilitados($id_proceso_disciplinario)
    {

        $query = TipoDerechoPeticionModel::query();

        $expediente = ClasificacionRadicadoModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)
            ->where('estado', Constants::ESTADOS['activo'])
            ->where('id_tipo_expediente', Constants::TIPO_EXPEDIENTE['derecho_peticion'])
            ->get();

        if (count($expediente) > 0) {

            if ($expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
                $query = $query->select('mas_tipo_derecho_peticion.*')->where('id', '<>', $expediente[0]->id_tipo_derecho_peticion)->orderBy('mas_tipo_derecho_peticion.nombre', 'asc')->get();
            }

            return TipoDerechoPeticionCollection::make($query);
        }

        $query = $query->select('mas_tipo_derecho_peticion.*')->orderBy('mas_tipo_derecho_peticion.nombre', 'asc')->get();

        return TipoDerechoPeticionCollection::make($query);
    }
}
