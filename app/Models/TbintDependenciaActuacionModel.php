<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbintDependenciaActuacionModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "tbint_dependencia_actuacion";

    public $timestamps = true;

    protected $fillable = [
        "id_dependencia",
        "id_dependencia_destino",
        "created_user",
        "updated_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user"
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    public function dependencia_origen() {
        return $this->hasOne(DependenciaOrigenModel::class, 'id', 'id_dependencia_destino');
    }

}
