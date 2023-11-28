<?php

namespace App\Http\Controllers\Api;

use Adldap\Query\Paginator;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConsecutivoDesgloseFormRequest;
use App\Http\Resources\ConsecutivoDesglose\ConsecutivoDesgloseCollection;
use App\Http\Resources\ConsecutivoDesglose\ConsecutivoDesgloseResource;
use App\Models\ConsecutivoDesgloseModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ConsecutivoDesgloseController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new ConsecutivoDesgloseModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        return ConsecutivoDesgloseCollection::make($this->repository->paginate($request->limit ?? 100));
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\CiudadFormRequest  $request
     * @return App\Http\Resources\Ciudad\CiudadResource
     */
    public function store(ConsecutivoDesgloseFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];
        $vigencia = $datosRequest["id_vigencia"];

        $consulta = DB::select("select * from mas_consecutivo_desglose where id_vigencia = " . $vigencia . "");
        DB::connection()->commit();

        if (count($consulta) >= 1) {

            $error['estado'] = false;
            "messageDetail";
            $error['error'] = 'Ya existe un registro con esta vigencia';
            return json_encode($error);
        } else if (count($consulta) == 0) {

            try {
                $datosRequest = $request->validated()["data"]["attributes"];
                return ConsecutivoDesgloseResource::make($this->repository->create($request->validated()["data"]["attributes"]));
            } catch (\Exception $e) {

                if (strpos($e->getMessage(), 'ORA-00001') !== false) {

                    $error['estado'] = false;
                    $error['error'] = 'Ya existe un registro con esta vigencia.';

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
        return ConsecutivoDesgloseResource::make($this->repository->find($id));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ConsecutivoDesgloseFormRequest $request,  $id)
    {
        return ConsecutivoDesgloseResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
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
