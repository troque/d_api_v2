<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoExpedienteMensajesModel extends Model
{
    use HasFactory;

    protected $table = "mas_tipo_expediente_mensajes";

    public $timestamps = true;

    protected $fillable = [
        "mensaje",
        "id_tipo_expediente",
        "id_sub_tipo_expediente",
        "estado",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    public function mas_tipo_expediente()
    {
        return $this->belongsTo(TipoExpedienteModel::class, "id_tipo_expediente", "id");
    }



    public function obtenerInformacionSubTipoExpediente($idTipoExpediente, $idSubTipoExpediente)
    {
        // Se valida que tipo de expediente
        if ($idTipoExpediente == 1) { // Derecho de petición

            // Se busca el sub tipo de expediente
            $tipoDerechoPeticion = TipoDerechoPeticionModel::where([
                ['id', '=', $idSubTipoExpediente],
            ])->take(1)->first();

            // Se valida que sea diferente de null la consulta
            if ($tipoDerechoPeticion != null) {

                // Se retorna el nombre
                return $tipoDerechoPeticion["nombre"];
            } else {

                // Se retorna vacio
                return "NO_APLICA";
            };
        } else if ($idTipoExpediente == 2 || $idTipoExpediente == 5) { // Poder referente a solicitud

            // Se retorna vacio
            return "NO_APLICA";
        } else if ($idTipoExpediente == 3) { // Queja

            // Se busca el sub tipo de expediente
            $tipoQueja = TipoQuejaModel::where([
                ['id', '=', $idSubTipoExpediente],
            ])->take(1)->first();

            // Se valida que sea diferente de null la consulta
            if ($tipoQueja != null) {

                // Se retorna el nombre
                return $tipoQueja["nombre"];
            } else {

                // Se retorna vacio
                return "NO_APLICA";
            };
        } else if ($idTipoExpediente == 4) { // Tutela

            // Se busca el sub tipo de expediente
            $terminoRespuesta = TerminoRespuestaModel::where([
                ['id', '=', $idSubTipoExpediente],
            ])->take(1)->first();

            // Se valida que sea diferente de null la consulta
            if ($terminoRespuesta != null) {

                // Se retorna el nombre
                return $terminoRespuesta["nombre"];
            } else {

                // Se retorna vacio
                return "NO_APLICA";
            };
        }
    }
}