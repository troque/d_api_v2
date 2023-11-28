<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DependenciaAccesoModel extends Model
{
    use HasFactory;

    protected $table = "mas_dependencia_acceso";

    public $timestamps = false;

    protected $fillable = [
        "nombre",
        "estado", 
    ];

}
