<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\RepositoryGeneric;
use App\Models\MasConsecutivoActuacionesModel;
use App\Http\Resources\MasConsecutivoActuaciones\MasConsecutivoActuacionesCollection;
use App\Http\Resources\MasConsecutivoActuaciones\MasConsecutivoActuacionesResource;
use App\Http\Requests\MasConsecutivoActuacionesFormRequest;

class MasConsecutivoActuacionesController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new MasConsecutivoActuacionesModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Se retorna el modelo de datos
        return MasConsecutivoActuacionesCollection::make($this->repository->paginate($request->limit ?? 100000000000));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MasConsecutivoActuacionesFormRequest $request)
    {
        // Se captura la informacion principal
        $data = $request->validated()["data"]["attributes"];

        // Se valida que ya exista un tipo de configuracion con los datos enviados
        $queryExiste = $this->repository->customQuery(function ($model) use ($data) {

            // Se realiza la consulta
            return $model->where('id_vigencia', $data['id_vigencia'])
                ->get();
        });

        // Se valida que ya existe para retornar el error
        if (!empty($queryExiste[0])) {

            // Se setea el error
            $error['error'] = 'Ya se encuentra registrado un consecutivo para esta vigencia';

            // Se retorna la informacion
            return json_encode($error);
        }

        // Se registra y retorna la informacion
        return MasConsecutivoActuacionesResource::make($this->repository->create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return MasConsecutivoActuacionesResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MasConsecutivoActuacionesFormRequest $request, $id)
    {


        // Se usa el trycatch
        try {
            // Se captura la informacion
            $data = $request->validated()["data"]["attributes"];
            $dataForm = isset($data["form"]) ? true : false;

            // Se valida cuando viene del formulario de actualizar
            if ($dataForm) {

                // Se retorna el update
                MasConsecutivoActuacionesResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
            } else {

                // Se inactiva el estado de los otros consecutivos
                MasConsecutivoActuacionesModel::where('ID', '!=', $id)->update(['estado' => 0]);

                // Se retorna el update
                MasConsecutivoActuacionesResource::make($this->repository->update($request->validated()["data"]["attributes"], $id));
            }
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'ORA-00001') !== false) {
                $error['estado'] = false;
                $mensaje = "Ocurrio un error al actualizar.";
                $error['error'] = strtoupper($mensaje);
                return json_encode($error);
            }
        }
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
