<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoEstadoProcesoModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_tipo_estado_proceso_disciplinario";

    public $timestamps = true;

    protected $fillable = [
        "nombre",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];
}
