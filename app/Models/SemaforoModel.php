<?php

namespace App\Models;

use Database\Seeders\MasDependenciaOrigenSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SemaforoModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "semaforo";

    public $timestamps = true;

    protected $fillable = [
        "nombre",
        "id_mas_evento_inicio",
        "id_etapa",
        "id_mas_actuacion_inicia",
        "id_mas_dependencia_inicia",
        "id_mas_grupo_trabajo_inicia",
        "nombre_campo_fecha",
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

    public function get_id_mas_evento_inicio() {
        return $this->belongsTo(MasEventoInicioModel::class,"id_mas_evento_inicio","id");
    }

    public function get_id_mas_actuacion_inicia() {
        return $this->belongsTo(MasActuacionesModel::class,"id_mas_actuacion_inicia","id");
    }

    public function get_id_mas_dependencia_inicia() {
        return $this->belongsTo(DependenciaOrigenModel::class,"id_mas_dependencia_inicia","id");
    }

    public function get_id_mas_grupo_trabajo_inicia() {
        return $this->belongsTo(GrupoTrabajoSecretariaComunModel::class,"id_mas_grupo_trabajo_inicia","id");
    }

    public function get_condiciones() {
        return $this->hasMany(CondicionModel::class,"id_semaforo","id");
    }

    public function get_procesoDisciplinario_por_semaforo() {
        return $this->belongsTo(ProcesoDisciplinarioPorSemaforoModel::class,"id_semaforo","id");
    }

    public function etapa() {
        return $this->belongsTo(MasEtapaModel::class,"id_etapa","id");
    }
}
