<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuid;
use Illuminate\Support\Facades\DB;

class CierreEtapaModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "cierre_etapa";

    public $timestamps = true;

    protected $fillable = [
        "id_proceso_disciplinario",
        "id_etapa",
        "id_funcionario_asignado",
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
    public function etapa() {
        return $this->belongsTo(EtapaModel::class,"id_etapa");
    }

    public function proceso_disciplinario() {
        return $this->belongsTo(ProcesoDiciplinarioModel::class,"id_proceso_disciplinario");
    }

    public function funcionarioAsignado() {
        return $this->belongsTo(User::class,"name","id_funcionario_asignado");
    }

    public function funcionarioRegistra() {
        return $this->belongsTo(User::class,"name","id_funcionario_asignado");
    }


    /**
     * Una clasificacion del radicado pertenece a un tipo de expediente, por eso la relación BelongsTo.
     */
   /* public function fase() {
        return $this->belongsTo(FaseModel::class,"id_tipo_queja");
    }*/
}
