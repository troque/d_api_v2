<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametrizacionActuacionesModel extends Model
{
    use HasFactory;

    protected $table = "mas_actuaciones";

    public $timestamps = true;

    protected $fillable = [
        "nombre_actuacion",
        "nombre_plantilla",
        "id_etapa",
        "estado",
        "id_etapa_despues_aprobacion",
        "despues_aprobacion_listar_actuacion",
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
}