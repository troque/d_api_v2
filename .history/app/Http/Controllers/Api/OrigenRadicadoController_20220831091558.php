<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrigenRadicadoFormRequest;
use App\Http\Resources\OrigenRadicado\OrigenRadicadoCollection;
use App\Http\Resources\OrigenRadicado\OrigenRadicadoResource;
use App\Models\OrigenRadicadoModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrigenRadicadoController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository) {
        $this->repository = $repository;
        $this->repository->setModel(new OrigenRadicadoModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return OrigenRadicadoCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    public function geOrigenRadicadoSinEstado()
    {


        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return OrigenRadicadoCollection::make($query);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrigenRadicadoFormRequest $request)
    { 
        $datosRequest = $request->validated();
        $nombre = $datosRequest["nombre"];
        $consulta = DB::select("
                select * from mas_origen_radicado
                where Translate(upper(nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu') = Translate(upper('".$nombre."'),'ÁáÉéÍíÓóÚú','AaEeIiOoUu')
            ");
        DB::connection()->commit();

       if(count($consulta) >= 1){

            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'Ya existe un registro con este radicado';
            return json_encode($error);


       }else if(count($consulta) == 0){

            try{

                $datosRequest = $request->validated();
                
                return OrigenRadicadoResource::make($this->repository->create($request->validated()));
                
                


            } catch (\Exception $e) {

                if(strpos($e->getMessage(), 'ORA-00001') !== false)  {

                    $error['estado'] = false;  
                    $error['error'] = 'Ya existe un registro con este radicado.';

                    return json_encode($error);
                }

            }

       }else{

            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'TENEMOS UN ERROR';
            return json_encode($error);
       }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MasOriginFiling  $masOriginFiling
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return OrigenRadicadoResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MasOriginFiling  $masOriginFiling
     * @return \Illuminate\Http\Response
     */
    public function update(OrigenRadicadoFormRequest $request, $id)
    {
        return OrigenRadicadoResource::make($this->repository->update($request->validated(), $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MasOriginFiling  $masOriginFiling
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrigenRadicadoModel $masOriginFiling)
    {
        //
    }
}
