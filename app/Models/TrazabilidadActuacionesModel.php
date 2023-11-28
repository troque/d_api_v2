<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TrazabilidadActuacionesModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "trazabilidad_actuaciones";

    public $timestamps = true;

    protected $fillable = [
        "uuid_actuacion",
        "id_estado_actuacion",
        "observacion",
        "estado",
        "id_dependencia",
        "created_user",
        "updated_user",
        "deleted_user"
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

    public function actuaciones()
    {
        return $this->belongsTo(ActuacionesModel::class, "uuid_actuacion", "uuid");
    }

    public function mas_estado_actuacion()
    {
        return $this->belongsTo(MasEstadoActuacionesModel::class, "id_estado_actuacion", "id");
    }

    public function dependencia()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "id_dependencia", "id");
    }

    public function usuario() {
        return $this->belongsTo(User::class,"created_user", "name");
    }

    public function actuacionesInactivas($uuidTrazabilidad) {

        $datos = DB::select(
            "
                SELECT
                    taa.uuid_actuacion AS id_actuacion,
                    ma.nombre_actuacion,
                    TO_CHAR(taa.created_at, 'DD/MM/YYYY HH:MI:SS AM') AS fecha_creacion,
                    taa.estado_anulacion_registro,
                    u.name AS nombre_usuario,
                    u.apellido AS apellido_usuario,
                    mdo.nombre AS nombre_dependencia,
                    me.nombre AS nombre_etapa
                FROM
                    trazabilidad_actuaciones_anuladas taa
                INNER JOIN actuaciones a ON taa.uuid_actuacion = a.uuid
                INNER JOIN mas_etapa me ON me.id = a.id_etapa
                INNER JOIN mas_actuaciones ma ON a.id_actuacion = ma.id
                INNER JOIN users u ON u.name = taa.created_user
                INNER JOIN mas_dependencia_origen mdo ON mdo.id = taa.id_dependencia
                WHERE taa.uuid_trazabilidad_actuaciones = '$uuidTrazabilidad'
            "
        );

        return $datos;
    }
}
