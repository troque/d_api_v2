<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\Traits\HasUuid;
use Illuminate\Support\Facades\DB;

class PortalNotificacionesModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "portal_notificaciones";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "uuid_proceso_disciplinario",
        "numero_documento",
        "tipo_documento",
        "detalle",
        "radicado",
        "estado",
        "id_actuacion",
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

    public function proceso_disciplinario()
    {
        return $this->belongsTo(ProcesoDiciplinarioModel::class, "uuid_proceso_disciplinario", "uuid");
    }

    public function usuario()
    {
        return $this->hasOne(User::class, "name", "created_user");
    }

    public function logs($id_notificacion)
    {
        return DB::select(
            "
                SELECT
                    pnl.descripcion,
                    SUBSTR(pnl.descripcion, 1, 100) AS descripcion_corta,
                    TO_CHAR(pnl.created_at, 'DD/MM/YYYY HH:MI:SS AM') AS fecha_creado,
                    (u.nombre || ' ' || u.apellido) AS nombre_completo,
                    mdo.nombre AS nombre_dependencia
                FROM
                    portal_notificaciones pn
                INNER JOIN portal_notificaciones_log pnl ON pnl.id_notificacion = pn.uuid
                INNER JOIN users u ON u.name = pnl.created_user
                INNER JOIN mas_dependencia_origen mdo ON mdo.id = pnl.id_dependencia
                WHERE pn.uuid = '" . $id_notificacion . "'
                ORDER BY pnl.created_at DESC
            "
        );
    }

    public function actuacion($id_actuacion)
    {
        return DB::select(
            "
                SELECT
                    a.uuid,
                    ma.nombre_actuacion,
                    a.auto
                FROM
                actuaciones a
                LEFT JOIN mas_actuaciones ma ON ma.id = a.id_actuacion
                WHERE a.uuid = '$id_actuacion'
            "
        );
    }
}