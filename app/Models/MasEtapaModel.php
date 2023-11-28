<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasEtapaModel extends Model
{
    use HasFactory;

    protected $table = "mas_etapa";

    public $timestamps = true;

    protected $fillable = [
        "nombre",
        "id_tipo_proceso",
        "estado_proder_preferente",
        "orden",
        "estado",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "created_user",
    ];

}
