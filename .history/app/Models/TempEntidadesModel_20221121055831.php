<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempEntidadesModel extends Model
{
    use HasFactory;

    protected $table = "temp_entidades";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_entidad",
        "direccion",
        "sector",
        "nombre_investigado",
        "cargo_investigado",
        "observaciones",
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
