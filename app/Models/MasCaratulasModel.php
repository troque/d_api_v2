<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasCaratulasModel extends Model
{
    use HasFactory;

    protected $table = "mas_caratulas";

    public $timestamps = true;

    protected $fillable = [
        "nombre",
        "nombre_plantilla",
        "estado",
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