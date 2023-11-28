<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempEntidadesModel extends Model
{
    use HasFactory;

    protected $table = "temp_clasificacion_radicado";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_temp_proceso_disciplinario",
        "id_etapa",
        "nombre_investigado",
        "id_entidad",
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
