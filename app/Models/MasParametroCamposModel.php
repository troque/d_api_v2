<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasParametroCamposModel extends Model
{
    use HasFactory;

    protected $table = "mas_parametro_campos";

    public $timestamps = true;

    protected $fillable = [
        "nombre_campo",
        "type",
        "value",
        "estado",
        "orden",
        "mostrar_en_tabla",
        "grupo_general",
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
