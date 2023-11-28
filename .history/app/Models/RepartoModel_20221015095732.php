<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuid;
use Illuminate\Support\Facades\DB;

/**
 * Esta clase permite identificar el tipo de reparto que se puede dar en las fases que necesiten reparto.
 * Cierre de etapa captura y reparto.
 * Cierre de etapa evaluación.
 */
class RepartoModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "cierre_etapa_configuracion";

    public $timestamps = true;

    protected $fillable = [
        "id_tipo_proceso_disciplinario",
        "id_tipo_expediente",
        "id_subtipo_expediente",
        "id_etapa",
        "id_fase",
        "id_tipo_cierre_etapa",
        "created_user",
        "updated_user",
        "deleted_user",
        "eliminado",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;


    public function tipo_proceso_disciplinario()
    {
        return $this->belongsTo(TipoProcesoModel::class, "id_tipo_proceso_disciplinario");
    }

    public function tipo_proceso_disciplinario()
    {
        return $this->belongsTo(TipoProcesoModel::class, "id_tipo_proceso_disciplinario");
    }


    /**
     * Una clasificacion del radicado pertenece a un tipo de expediente, por eso la relación BelongsTo.
     */
    public function etapa()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa");
    }
}
