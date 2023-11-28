<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentoCierre\DocumentoCierreCollection;
use App\Http\Resources\DocumentoCierre\DocumentoCierreResource;
use App\Http\Resources\DocumentoSirius\DocumentoSiriusCollection;
use App\Models\DocumentoCierreModel;
use App\Models\DocumentoSiriusModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class DocumentoCierreController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository) {
        $this->repository = $repository;
        $this->repository->setModel(new DocumentoCierreModel());
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    }

    public function showDocumentosCierre($id_proceso_disciplinario, $id_etapa, $id_fase)
    {
        try {

            $respuesta_query_documento_cierre = $this->repository->customQuery(
                function ($model) use ($id_proceso_disciplinario){
                    return $model
                    ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
                    ->orderby('created_at', 'desc')
                    ->get();
                }
            );

            if($respuesta_query_documento_cierre->count() <= 0){
                return DocumentoCierreCollection::make($respuesta_query_documento_cierre);
            }
            else{
                $repository_proceso_disciplinario = new RepositoryGeneric();
                $repository_proceso_disciplinario->setModel(new DocumentoSiriusModel());

                $respuesta_query_documento_sirius = $repository_proceso_disciplinario->customQuery(function ($model) use ($id_proceso_disciplinario, $id_etapa, $id_fase)
                {
                    return $model
                    ->where('id_proceso_disciplinario', $id_proceso_disciplinario)
                    ->where('id_etapa', $id_etapa)
                    ->where('id_fase', $id_fase)
                    ->get();
                });

                if($respuesta_query_documento_sirius->count() > 0){
                    return DocumentoSiriusCollection::make($respuesta_query_documento_sirius)->documentoCierre($respuesta_query_documento_cierre[0]);
                }
                else{
                    return DocumentoCierreResource::make($respuesta_query_documento_cierre[0]);
                }
            }

        } catch (\Exception $e) {
            error_log($e);
            dd($e);
            // Woopsy
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
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
}
