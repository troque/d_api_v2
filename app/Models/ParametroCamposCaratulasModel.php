<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametroCamposCaratulasModel extends Model
{
    use HasFactory;

    protected $table = "mas_parametro_campos_caratula";

    public $timestamps = true;

    protected $fillable = [
        "nombre_campo",
        "type",
        "value",
        "estado",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "updated_user",
        "deleted_user",
    ];
}