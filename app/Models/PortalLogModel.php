<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PortalLogModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "portal_log";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "portal_id_user",
        "detalle",
        "informacion_equipo",
        "estado",
        "created_user",
        "updated_user",
        "delete_user",
        "created_at",
        "updated_at",
    ];

    protected $hidden = [
        "created_user",
        "updated_user",
        "deleted_user",
        "created_at",
        "updated_at",
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    public function mas_interesados($idUser)
    {
        $informacionInteresados = DB::select("SELECT MTD.NOMBRE || '- ' || I.NUMERO_DOCUMENTO || ' - ' || I.PRIMER_NOMBRE || ' ' || I.SEGUNDO_NOMBRE || ' ' || I.PRIMER_APELLIDO || ' ' || I.SEGUNDO_APELLIDO AS INTERESADO
                                  FROM INTERESADO I
                                  INNER JOIN MAS_TIPO_DOCUMENTO MTD ON MTD.ID = I.TIPO_DOCUMENTO
                                  WHERE I.NUMERO_DOCUMENTO = (SELECT NUMERO_DOCUMENTO
                                                              FROM PORTAL_USERS
                                                              WHERE ID = '$idUser')
                                  AND I.TIPO_DOCUMENTO = (SELECT TIPO_DOCUMENTO
                                                          FROM PORTAL_USERS
                                                          WHERE ID = '$idUser')");

        return $informacionInteresados[0];
    }
}