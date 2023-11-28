<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncorporacionModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "incorporacion";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_proceso_disciplinario_expediente",
        "id_proceso_disciplinario_incorporado",
        "id_dependencia_origen",
        "expediente",
        "vigencia_expediente",
        "version",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    public function dependencia_origen() {
        return $this->belongsTo(DependenciaOrigenModel::class,"id_dependencia_origen");
    }

    public function log_proceso_disciplinario_incorporado() {
        return $this->hasOne(LogProcesoDisciplinarioModel::class,"id_proceso_disciplinario", "id_proceso_disciplinario_incorporado")->latest('created_at');
    }

    public function log_proceso_disciplinario_expediente() {
        return $this->hasOne(LogProcesoDisciplinarioModel::class,"id_proceso_disciplinario", "id_proceso_disciplinario_expediente")->latest('created_at');
    }

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;
}
