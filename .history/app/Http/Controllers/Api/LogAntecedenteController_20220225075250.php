<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LogAntecedenteFormRequest;
use App\Http\Resources\LogAntecedente\LogAntecedenteCollection;
use App\Http\Resources\LogAntecedente\LogAntecedenteResource;
use App\Models\LogAntecedenteModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class LogEtapaController extends Controller
{

    private $repository;

    public function __construct(RepositoryGeneric $repository) {
        $this->repository = $repository;
        $this->repository->setModel(new LogAntecedenteModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return LogEtapaCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LogAntecedenteFormRequest $request)
    {
        try {

            $datosRequest = $request->validated();
            return LogAntecedenteResource::make($this->repository->create($datosRequest));


        } catch (QueryException  $e) {

            $error['estado'] = false;
            $error['error'] = $e->getMessage();
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
        return LogAntecedenteResource::make($this->repository->find($id));
    }


   /**
    *
    */
    public function getAllAntecedentesByIdProcesoDisciplinario($antecedenteUUID, LogAntecedenteFormRequest $request){

        $datosRequest = $request->validated();

        $query = $this->repository->customQuery(function ($model) use ($antecedenteUUID, $datosRequest)
        {
            return $model->where('id_antecedente', $antecedenteUUID)
            ->where('estado', $datosRequest['estado'])
            ->orderBy('antecedente.created_at', 'desc')
            ->get();
        });


        return LogAntecedenteCollection::make($query);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){

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
