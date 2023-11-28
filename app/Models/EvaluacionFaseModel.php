<?php

namespace App\Models;

use App\Http\Utilidades\Constants;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluacionFaseModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "evaluacion_fase";

    public $timestamps = true;

    protected $fillable = [
        "id_fase_actual",
        "id_fase_antecesora",
        "id_resultado_evaluacion",
        "id_tipo_expediente",
        "id_sub_tipo_expediente",
        "orden",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "updated_user",
        "deleted_user",
    ];

    public function fase_actual()
    {
        return $this->belongsTo(FaseModel::class, "id_fase_actual");
    }

    public function fase_antecesora()
    {
        return $this->belongsTo(FaseModel::class, "id_fase_antecesora");
    }

    public function tipo_evaluacion()
    {
        return $this->belongsTo(TipoEvaluacionModel::class, "id_resultado_evaluacion");
    }

    public function tipo_expediente()
    {
        return $this->belongsTo(TipoExpedienteModel::class, "id_tipo_expediente");
    }

    public function tipo_sub_tipo_expediente()
    {

        if ($this->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['derecho_peticion']) {
            return $this->belongsTo(TipoDerechoPeticionModel::class, "id_sub_tipo_expediente");
        } else if ($this->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['poder_referente']) {
            return $this->belongsTo(TipoQuejaModel::class, "id_sub_tipo_expediente");
        } else if ($this->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['queja'] || $this->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['proceso_disciplinario']) {
            return $this->belongsTo(TipoQuejaModel::class, "id_sub_tipo_expediente");
        } else if ($this->id_tipo_expediente == Constants::TIPO_EXPEDIENTE['tutela']) {
            return $this->belongsTo(TerminoRespuestaModel::class, "id_sub_tipo_expediente");
        }
    }

    protected $primaryKey = 'id';
    public $incrementing = true;
}
