<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DiasNoLaboralesFormRequest;
use App\Http\Resources\DiasNoLaborales\DiasNoLaboralesCollection;
use App\Http\Resources\DiasNoLaborales\DiasNoLaboralesResource;
use App\Http\Resources\DiasNoLaborales\DiasNoLaboralesListResource;
use App\Models\DiasNoLaboralesModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class DiasNoLaboralesController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new DiasNoLaboralesModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $estado = $request->query('estado');
        if ($estado != null)
            $query = DiasNoLaboralesModel::where("estado", $estado)->orderBy("fecha", 'desc')->get();
        else
            $query = DiasNoLaboralesModel::orderBy("fecha")->get();

        return DiasNoLaboralesCollection::make($query);
        //return DiasNoLaboralesCollection::make($this->repository->paginate($request->limit ?? 10));
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\DiasNoLaboralesFormRequest  $request
     * @return App\Http\Resources\DiasNoLaborales\DiasNoLaboralesResource
     */
    public function store(DiasNoLaboralesFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];

        $query = new RepositoryGeneric();
        $query->setModel(new DiasNoLaboralesModel());
        $queryDia = $query->customQuery(function ($model) use ($datosRequest) {
            return
                $model->whereDate('fecha', $datosRequest['fecha'])
                ->select('mas_dias_no_laborales.id', 'mas_dias_no_laborales.fecha', 'mas_dias_no_laborales.estado')
                ->get();
        });
        $respuesta = '';
        if (!empty($queryDia[0])) {
            $respuesta = DiasNoLaboralesModel::whereDate('fecha', $datosRequest['fecha'])->update(['estado' => '1']);
        } else {
            $respuesta = DiasNoLaboralesResource::make($this->repository->create($datosRequest));
        }
        return json_encode($respuesta);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return DiasNoLaboralesResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DiasNoLaboralesFormRequest $request,  $id)
    {
        return DiasNoLaboralesResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
