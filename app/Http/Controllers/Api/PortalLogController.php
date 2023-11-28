<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PortalLogModel;
use App\Repositories\RepositoryGeneric;
use App\Http\Resources\PortalLog\PortalLogCollection;
use Illuminate\Support\Facades\DB;

class PortalLogController extends Controller
{
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new PortalLogModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->repository->customQuery(function ($model) {
            return $model->orderBy('created_at', 'desc')
                ->get();
        });

        return PortalLogCollection::make($query);
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

    public function buscarUsuario($cedula)
    {
        $usuario = DB::select(
            "
                SELECT
                    pu.id,
                    i.email,
                    (i.primer_nombre || ' ' || i.segundo_nombre || ' ' || i.primer_apellido || ' ' || i.segundo_apellido) AS nombre
                FROM
                    portal_users pu
                INNER JOIN interesado i ON i.numero_documento = pu.numero_documento
                WHERE i.numero_documento = '$cedula'
            "
        );

        if(count($usuario) <= 0){
            $error['estado'] = false;
            $error['error'] = 'Ya existe un registro con este nombre.';
            return json_encode($error);
        }

        $query = $this->repository->customQuery(function ($model) use ($usuario) {
            return $model
                ->where('portal_id_user', $usuario[0]->id)
                ->orderBy('created_at', 'desc')
                ->get();
        });

        $resultado['usuario'] = $usuario[0];
        $resultado['log'] = PortalLogCollection::make($query);
        
        return json_encode($resultado);
    }
}
