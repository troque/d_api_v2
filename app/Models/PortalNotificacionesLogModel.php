<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalNotificacionesLogModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "portal_notificaciones_log";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_notificacion",
        "id_dependencia",
        "descripcion",
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
}
