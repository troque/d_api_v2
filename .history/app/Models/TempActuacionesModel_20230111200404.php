<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempActuacionesModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "temp_actuaciones";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "terminomonto",
        "nombre",
        "tipo",
        "autonumero",
        "fecha",
        "fechatermino",
        "instancia",
        "decision",
        "observacion",
        "radicado",
        "vigencia",
        "item",
        "path",
        "created_user",
        "updated_user",
        "deleted_user",

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
