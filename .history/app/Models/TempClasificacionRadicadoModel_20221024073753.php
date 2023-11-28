<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempClasificacionRadicadoModel extends Model
{
    use HasFactory;

    protected $table = "temp_antecedentes";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_temp_proceso_disciplinario",
        "descripcion",
        "fecha_registro",
        "estado",
        "id_etapa",
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
