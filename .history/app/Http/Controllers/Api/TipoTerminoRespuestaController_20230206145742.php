<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TipoTerminoRespuesta\TipoTerminoRespuestaCollection;
use App\Http\Utilidades\Constants;
use App\Models\ClasificacionRadicadoModel;
use App\Models\TerminoRespuestaModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class TipoTerminoRespuestaController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new TerminoRespuestaModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return TipoTerminoRespuestaCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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


    public function getTiposTerminosRespuestaTutelaHabilitados($id_proceso_disciplinario)
    {

        $query = TerminoRespuestaModel::query();

        $expediente = ClasificacionRadicadoModel::where("id_proceso_disciplinario", $id_proceso_disciplinario)
            ->where('estado', Constants::ESTADOS['activo'])
            ->where('id_tipo_expediente', Constants::TIPO_EXPEDIENTE['tutela'])
            ->get();

        if (count($expediente) > 0) {

            if ($expediente[0]->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']) {
                $query = $query->select('mas_termino_respuesta.id', 'mas_termino_respuesta.nombre', 'mas_termino_respuesta.estado')->where('id', '<>', $expediente[0]->id_termino_respuesta)->orderBy('mas_termino_respuesta.nombre', 'asc')->get();
            }

            return TipoTerminoRespuestaCollection::make($query);
        }

        $query = $query->select('mas_termino_respuesta.id', 'mas_termino_respuesta.nombre', 'mas_termino_respuesta.estado')->orderBy('mas_termino_respuesta.nombre', 'asc')->get();

        return TipoTerminoRespuestaCollection::make($query);
    }
}
