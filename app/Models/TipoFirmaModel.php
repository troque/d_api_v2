<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoFirmaModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_tipo_firma";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "nombre",
        "estado",
        "tamano",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];
}
