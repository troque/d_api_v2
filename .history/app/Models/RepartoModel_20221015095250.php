<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuid;
use Illuminate\Support\Facades\DB;

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


    /**
     * Una clasificacion del radicado pertenece a un tipo de expediente, por eso la relación BelongsTo.
     */
    public function etapa()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa");
    }

    public function proceso_disciplinario()
    {
        return $this->belongsTo(ProcesoDiciplinarioModel::class, "id_proceso_disciplinario");
    }

    public function funcionarioAsignado()
    {
        return $this->belongsTo(User::class, "id_funcionario_asignado", "name");
    }

    public function funcionarioRegistra()
    {
        return $this->belongsTo(User::class, "created_user", "name");
    }


    /**
     * Una clasificacion del radicado pertenece a un tipo de expediente, por eso la relación BelongsTo.
     */
    /* public function fase() {
        return $this->belongsTo(FaseModel::class,"id_tipo_queja");
    }*/
}
