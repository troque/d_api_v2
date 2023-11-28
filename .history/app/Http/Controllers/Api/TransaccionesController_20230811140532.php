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


            try {
                $usuarioARemitir = User::where('name', $datosRecibidos['usuario_a_remitir'])->first();
                $procesoDisciplinario = ProcesoDiciplinarioModel::where('uuid', $datosRecibidos['id_proceso_disciplinario'])->first();

                $correos = $usuarioARemitir->email;
                $asunto = "SINPROC: (" . $procesoDisciplinario->radicado . ") - VIGENCIA (" . $procesoDisciplinario->vigencia . ')';
                $contenido = "Se ha sido asignado el siguiente proceso disciplinario. SINPROC: (" . $procesoDisciplinario->radicado . ") - VIGENCIA (" . $procesoDisciplinario->vigencia . '), ' . substr($datosRecibidos['descripcion_a_remitir'], 0, 4000);
                $archivos = null;
                $correoscc = null;
                $correosbbc = null;

                // Se captura la informacion del usuario
                $nombreGet = !empty($usuarioARemitir->nombre) ? $usuarioARemitir->nombre . " " : "";
                $apellidoGet = !empty($usuarioARemitir->apellido) ? $usuarioARemitir->apellido : "";
                $nombre_usuario = $nombreGet . $apellidoGet;

                MailTrait::sendMail(
                    $correos,
                    $nombre_usuario,
                    $asunto,
                    $contenido,
                    $archivos,
                    $correoscc,
                    $correosbbc
                );
            } catch (\Exception $th) {
                error_log($th);
            }

            DB::connection()->commit();
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
