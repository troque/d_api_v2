<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoRespuestaModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "entidad_funcionario_queja_interna";

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
