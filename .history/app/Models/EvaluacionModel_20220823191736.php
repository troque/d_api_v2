<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluacionModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "evaluacion";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "id_proceso_disciplinario",
        "noticia_priorizada",
        "justificacion",
        "estado",
        "resultado_evaluacion",
        "tipo_conducta",
        "created_at",
        "created_user",
        "updated_user",
        "deleted_user",
        "estado_evaluacion",
        "id_etapa",
    ];

    protected $hidden = [

        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    public function resultado_evaluacion_entidad() {
        return $this->belongsTo(ResultadoEvaluacionModel::class, "resultado_evaluacion");
    }

    public function tipo_conducta_entidad() {
        return $this->belongsTo(TipoConductaModel::class, "tipo_conducta");
    }
    public function usuario() {
        return $this->belongsTo(User::class,"created_user","name");
    }

    public function fases_permitidas() {
        return $this->belongsToMany(FaseModel::class, 'EVALUACION_RESULTADO_PERMITIDO', 'resultado_evaluacion_id','fase_id','resultado_evaluacion');
    }

    public function etapa() {
        return $this->belongsTo(EtapaModel::class,"id_etapa");
    }






}
