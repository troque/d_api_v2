<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorModel extends Model
{
    use HasFactory;

    protected $connection = 'ORA_SINPROC';

    protected $table = "sector";

    protected $fillable = [
        "idsector",
        "idestado",
        "nombre",
    ];

}
