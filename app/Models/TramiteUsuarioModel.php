<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TramiteUsuarioModel extends Model
{
    use HasFactory;

    protected $connection = 'ORA_SINPROC';

    protected $table = "tramiteusuario";

    protected $fillable = [
        "id_tramite"
    ];

}
