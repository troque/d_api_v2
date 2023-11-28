<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransaccionesFormRequest;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Http\Utilidades\Constants;
use App\Models\LogProcesoDisciplinarioModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Traits\MailTrait;
use App\Http\Controllers\Traits\TrazabilidadTrait;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\User;

class TransaccionesController extends Controller
{

    use MailTrait;
    use TrazabilidadTrait;

    public function CambiarUsuarioProcesoDisciplinario(TransaccionesFormRequest $request)
    {
        try {

            DB::connection()->beginTransaction();

            $datosRecibidos = $request->validated()["data"]["attributes"];

            $respuesta = $this->enviarProcesoDisciplinarioAUsuario($datosRecibidos);

            DB::connection()->commit();

            try {
                $this->enviarCorreoUsuario($datosRecibidos);
            } catch (\Exception $th) {
                error_log($th);
            }

            return $respuesta;
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }
}
