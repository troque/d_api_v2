<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequerimientoJuzgadoModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "requerimiento_juzgado";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_etapa",
        "id_proceso_disciplinario",
        "id_dependencia_origen",
        "id_dependencia_destino",
        "id_clasificacion_radicado",
        "enviar_otra_dependencia",
        "id_proceso_disciplinario",
        "descripcion",
        "id_funcionario_asignado",
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

    public function funcionarioRegistra() {
        return $this->belongsTo(User::class,"created_user","name");
    }

    public function funcionarioAsignado() {
        return $this->belongsTo(User::class,"id_funcionario_asignado","name");
    }

    public function dependenciaOrigen() {
        return $this->belongsTo(DependenciaOrigenModel::class,"id_dependencia_origen");
    }

    public function dependenciaDestino() {
        return $this->belongsTo(DependenciaOrigenModel::class,"id_dependencia_destino");
    }

    public function clasificacionRadicado(){
        return $this->belongsTo(ClasificacionRadicadoModel::class,"id_clasificacion_radicado");
    }

    /**
     *
     */
    public function getDescripcionCorta(){

        if(strlen ($this['descripcion'])>=50){

            return substr($this['descripcion'], 0, 50);
        }

        return $this['descripcion'];
    }
}
