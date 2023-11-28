<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcesoSiriusModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "proceso_sirius";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "fecha_ingreso",
        "radicado_entidad",

        "id_proceso_disciplinario",
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

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

}
