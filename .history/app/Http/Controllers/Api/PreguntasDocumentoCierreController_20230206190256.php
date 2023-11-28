<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreguntasDocumentoCierreFormRequest;
use App\Http\Requests\PreguntasDocumentoCierreFormRequestActiva;
use App\Http\Resources\PreguntasDocumentoCierre\PreguntasDocumentoCierreCollection;
use App\Http\Resources\PreguntasDocumentoCierre\PreguntasDocumentoCierreResource;
use App\Models\PreguntasDocumentoCierreModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreguntasDocumentoCierreController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new PreguntasDocumentoCierreModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return PreguntasDocumentoCierreCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PreguntasDocumentoCierreFormRequest $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return PreguntasDocumentoCierreResource::make($this->repository->find($id));
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

    /**
     *
     */
    public function preguntas()
    {
        $query = $this->repository->customQuery(function ($model) {
            return $model->get();
        });

        return PreguntasDocumentoCierreCollection::make($query);
    }


    public function estadoPreguntas()
    {
        $estado_preguntas = DB::select("select id, estado from mas_preguntas_doc_cierre");


        if (count($estado_preguntas) > 0) {

            $array = array(); //creamos un array

            for ($cont = 0; $cont < count($estado_preguntas); $cont++) {

                if ($estado_preguntas[$cont]->id == 1) {
                    $reciboDatos['attributes']['preguntas_documento_cierre'] = $estado_preguntas[$cont]->estado;
                } else if ($estado_preguntas[$cont]->id == 2) {
                    $reciboDatos['attributes']['compulsan_copias'] = $estado_preguntas[$cont]->estado;
                }
            }

            array_push($array, $reciboDatos);
            $json['data'] = $array;
            return json_encode($json);
        }

        return null;
    }

    /**
     *
     */
    public function updatePreguntas(PreguntasDocumentoCierreFormRequestActiva $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];

        if ($datosRequest['requiere_documento'] == "" || $datosRequest['requiere_documento'] == false || $datosRequest['requiere_documento'] == null) {
            $datosRequest['requiere_documento'] = 0;
        }

        if ($datosRequest['compulsa_copias'] == "" || $datosRequest['compulsa_copias'] == false || $datosRequest['compulsa_copias'] == null) {
            $datosRequest['compulsa_copias'] = 0;
        }

        PreguntasDocumentoCierreModel::where('id', 1)->update(['estado' => $datosRequest['requiere_documento']]);
        PreguntasDocumentoCierreModel::where('id', 2)->update(['estado' => $datosRequest['compulsa_copias']]);

        $query = $this->repository->customQuery(function ($model) {
            return $model->get();
        });

        return PreguntasDocumentoCierreCollection::make($query);
    }
}
