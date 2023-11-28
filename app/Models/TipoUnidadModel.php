<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoUnidadModel extends Model
{
    use HasFactory;

    protected $table = "mas_tipo_unidad";

    public $timestamps = true;

    protected $fillable = [
        "nombre",
        "codigo_unidad",
        "descripcion_unidad",
        "id_dependencia",
        "estado",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    public function mas_dependencia_origen()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "id_dependencia", "id");
    }
}