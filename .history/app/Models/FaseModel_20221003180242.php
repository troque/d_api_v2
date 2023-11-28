<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaseModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_fase";

    public $timestamps = true;

    protected $fillable = [
        "nombre",
        "estado",
        "id_etapa",
        "created_user",
        "updated_user",
        "deleted_user",
        "orden",
        "link_pagina_migracion",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    public function etapa() {
        return $this->belongsTo(EtapaModel::class, "id_etapa");
    }
}
