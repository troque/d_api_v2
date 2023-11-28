<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ValidarClasificacionModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "validar_clasificacion";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_clasificacion_radicado",
        "id_etapa",
        "estado",
        "id_proceso_disciplinario",
        "created_user",
        "updated_user",
        "deleted_user",
        "eliminado",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "updated_user",
        "deleted_user",
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;


    public function etapa() {
        return $this->belongsTo(EtapaModel::class,"id_etapa");
    }

    public function usuario() {
        return $this->belongsTo(User::class,"created_user","name");
    }

    public function clasificacion_radicado() {
        return $this->belongsTo(ClasificacionRadicadoModel::class,"id_clasificacion_radicado");
    }

    public function proceso_disciplinario() {
        return $this->belongsTo(ProcesoDiciplinarioModel::class, "id_proceso_disciplinario");        
    }
}
