<?php

namespace App\Models;

use App\Http\Utilidades\Constants;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogProcesoDisciplinarioModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "log_proceso_disciplinario";

    public $timestamps = true;

    protected $fillable = [
        "id_proceso_disciplinario",
        "id_etapa",
        "id_fase",
        "id_tipo_log",
        "id_estado",
        "descripcion",
        "id_dependencia_origen",
        "documentos",
        "id_funcionario_actual",
        "id_funcionario_registra",
        "created_user",
        "updated_user",
        "deleted_user",
        "id_fase_registro",
        "id_tipo_transaccion",
        "id_funcionario_asignado",
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


    public function etapa() {
        return $this->belongsTo(EtapaModel::class,"id_etapa");
    }

    public function fase() {
        return $this->belongsTo(FaseModel::class,"id_fase");
    }

    public function dependencia_origen() {
        return $this->belongsTo(DependenciaOrigenModel::class,"id_dependencia_origen");
    }

    public function estado_etapa() {
        return $this->belongsTo(TipoEstadoEtapaModel::class,"id_estado");
    }

    public function funcionario_registra() {
        return $this->belongsTo(User::class,"id_funcionario_registra","name");
    }

    public function funcionario_actual() {
        return $this->belongsTo(User::class,"id_funcionario_actual","name");
    }

    public function tipo_log() {
        return $this->belongsTo(TipoLogModel::class,"id_tipo_log");
    }

    public function tipo_transaccion() {
        return $this->belongsTo(TipoTransaccionModel::class,"id_tipo_transaccion");
    }

    public function clasificacion_radicado() {
        return $this->belongsTo(ClasificacionRadicadoModel::class,"id_fase_registro");
    }

    public function funcionario_asignado() {
        return $this->belongsTo(User::class,"id_funcionario_asignado","name");
    }

    /**
     *
     */
    public function getDescripcionCorta(){

        if(strlen ($this['descripcion'])>=150){

            return substr($this['descripcion'], 0, 150);
        }

        return $this['descripcion'];
    }

}
