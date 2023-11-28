<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TbintDendenciaOrigenFaseModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tbint_dependencia_origen_fase";

    public $timestamps = true;

    protected $fillable = [
        "id_dependencia",
        "id_etapa",
        "estado",
        "fecha_ingreso",
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

    public function dependencia_origen() {
        return $this->belongsTo(DependenciaOrigenModel::class,"id_dependencia_origen");
    }

}
