<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortalConfiguracionTipoInteresadoFormRequest;
use App\Http\Resources\PortalConfiguracionTipoInteresado\PortalConfiguracionTipoInteresadoCollection;
use App\Http\Resources\PortalConfiguracionTipoInteresado\PortalConfiguracionTipoInteresadoResource;
use Illuminate\Http\Request;
use App\Repositories\RepositoryGeneric;
use App\Models\PortalConfiguracionTipoInteresadoModel;

class PortalConfiguracionTipoInteresadoController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new PortalConfiguracionTipoInteresadoModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return PortalConfiguracionTipoInteresadoCollection::make($this->repository->paginate($request->limit ?? 500));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PortalConfiguracionTipoInteresadoFormRequest $request)
    {
        // Se captura la informacion principal
        $data = $request->validated()["data"]["attributes"];

        // Se valida que ya exista un tipo de configuracion con los datos enviados
        $queryExiste = $this->repository->customQuery(function ($model) use ($data) {

            // Se realiza la consulta
            return $model->where('id_tipo_sujeto_procesal', $data['id_tipo_sujeto_procesal'])
                ->where('id_tipo_interesado', $data['id_tipo_interesado'])
                ->get();
        });

        // Se valida que ya existe para retornar el error
        if (!empty($queryExiste[0])) {

            // Se setea el error
            $error['error'] = 'Ya se encuentra registrado este tipo de configuración';

            // Se retorna la informacion
            return json_encode($error);
        }

        // Se registra y retorna la informacion
        return PortalConfiguracionTipoInteresadoResource::make($this->repository->create($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return PortalConfiguracionTipoInteresadoResource::make($this->repository->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PortalConfiguracionTipoInteresadoFormRequest $request, $id)
    {

        // Se captura la informacion principal
        $data = $request->validated()["data"]["attributes"];

        // Se valida que ya exista un tipo de configuracion con los datos enviados
        $queryExiste = $this->repository->customQuery(function ($model) use ($data) {

            // Se realiza la consulta
            return $model->where('id_tipo_sujeto_procesal', $data['id_tipo_sujeto_procesal'])
                ->where('id_tipo_interesado', $data['id_tipo_interesado'])
                ->get();
        });

        // Se valida que ya existe para retornar el error
        if (!empty($queryExiste[0])) {
            if($queryExiste[0]->uuid !== $id){
                // Se setea el error
                $error['error'] = 'Ya se encuentra registrado este tipo de configuración';
    
                // Se retorna la informacion
                return json_encode($error);
            }
        }

        // Se actualiza y retorna la informacion
        return PortalConfiguracionTipoInteresadoResource::make($this->repository->update($data, $id));
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
