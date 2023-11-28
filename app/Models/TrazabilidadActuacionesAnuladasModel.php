<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrazabilidadActuacionesAnuladasModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "trazabilidad_actuaciones_anuladas";

    public $timestamps = true;

    protected $fillable = [
        "uuid_trazabilidad_actuaciones",
        "uuid_actuacion",
        "id_dependencia",
        "estado_anulacion_registro",
        "created_user",
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
