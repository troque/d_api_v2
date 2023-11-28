<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoFinalizaModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "auto_finaliza";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "id_semaforo",
        "id_etapa",
        "id_mas_actuacion",
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

    public function get_id_mas_actuacion() {
        return $this->belongsTo(MasActuacionesModel::class,"id_mas_actuacion","id");
    }

    public function get_id_etapa() {
        return $this->belongsTo(MasEtapaModel::class,"id_etapa","id");
    }
}
