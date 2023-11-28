<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InteresadoEntidadPermitidaModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_entidad_permitida";
    public $timestamps = true;

    protected $fillable = [
        "id_entidad",
        "estado",
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
