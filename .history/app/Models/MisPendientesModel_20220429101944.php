<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MisPendientesModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "proceso_disciplinario";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "radicado",
        "vigencia",
        "id_tipo_proceso",
        "id_origen_radicado",
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
