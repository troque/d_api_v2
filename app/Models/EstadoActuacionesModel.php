<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoActuacionesModel extends Model
{
    use HasFactory;

    protected $table = "mas_estado_actuaciones";

    public $timestamps = true;

    protected $fillable = [
        "nombre",
        "codigo",
        "descripcion",
        "created_user",
        "updated_user",
        "deleted_user"
    ];

    protected $hidden = [
        "created_user",
        "updated_user",
        "deleted_user",
        "created_at",
        "updated_at",
    ];
}