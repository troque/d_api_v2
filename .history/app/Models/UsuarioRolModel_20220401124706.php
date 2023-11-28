<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioRolModel extends Model
{
    use HasFactory;

    protected $connection = 'ORA_SINPROC';

    protected $table = "usuario_rol";

    protected $fillable = [
        "nombre",
        "apellido",
        "cedula"
    ];

}
