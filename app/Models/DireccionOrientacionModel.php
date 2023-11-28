<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DireccionOrientacionModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_direccion_orientacion";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "nombre",
        "estado",
        "created_user",
        "updated_user",        
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "created_user",
        "updated_user",
    ];
}
