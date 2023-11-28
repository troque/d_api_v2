<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActuacionPorSemaforoModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "actuacion_por_semaforo";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "id_semaforo",
        "id_interesado",
        "id_actuacion",
        "fecha_inicio",
        "fecha_fin",
        "observaciones",
        "finalizo",
        "fechafinalizo",
        "estado",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function get_id_semaforo() {
        return $this->belongsTo(SemaforoModel::class,"id_semaforo","id");
    }

    public function get_id_interesado() {
        return $this->belongsTo(DatosInteresadoModel::class,"id_interesado","uuid");
    }

    public function get_id_actuacion() {
        return $this->belongsTo(ActuacionesModel::class,"id_actuacion","uuid");
    }

    public function get_condiciones() {
        return $this->hasMany(CondicionModel::class,"id_semaforo","id_semaforo");
    }
}
