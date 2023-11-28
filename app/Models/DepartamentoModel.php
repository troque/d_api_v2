<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartamentoModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_departamento";

    public $timestamps = true;

    protected $fillable = [
        "nombre",
        "codigo_dane",
        "estado",
        "created_user",
        "updated_user",
        "deleted_user",
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

    public function ciudades() {
        return $this->hasMany(CiudadModel::class, 'id_departamento');
    }
}
