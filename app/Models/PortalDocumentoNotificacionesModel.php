<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalDocumentoNotificacionesModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "portal_documento_notificaciones";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "uuid_notificaciones",
        "documento",
        "extension",
        "tamano",
        "ruta",
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

    public function notificaciones_pw()
    {
        return $this->belongsTo(NotificacionesPwModel::class, "uuid_notificaciones", "uuid");
    }
}