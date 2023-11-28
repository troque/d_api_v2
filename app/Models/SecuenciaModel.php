<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecuenciaModel extends Model
{
    use HasFactory;

    protected $connection = 'ORA_SINPROC';

    protected $table = "Binconsecutivo";

    protected $fillable = [
        "grupo",
        "nombre",
        "vigencia",
        "codigo_compania",
        "codigo_unidad_ejecutora",
        "descripcion",
        "secuencial",
    ];

}
