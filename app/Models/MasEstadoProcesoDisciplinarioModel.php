<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasEstadoProcesoDisciplinarioModel extends Model
{
    use HasFactory;

    protected $table = "mas_estado_proceso_disciplinario";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "nombre",
        "estado",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "updated_user",
        "deleted_user",
    ];
}
