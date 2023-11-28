<?php

namespace App\Models;

use App\Models\DepartamentoModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CiudadModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_ciudad";

    public $timestamps = true;

    protected $fillable = [
        "nombre",
        "estado",
        "codigo_dane",
        "id_departamento",
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

    public function departamento() {
        return $this->belongsTo(DepartamentoModel::class, "id_departamento");
    }
}
