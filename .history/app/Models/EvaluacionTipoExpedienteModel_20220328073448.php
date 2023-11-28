<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AntecedenteModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "antecedente";

    public $timestamps = true;

    protected $fillable = [
        "descripcion",
        "fecha_registro",
        "fecha_auto",
        "id_dependencia",
        "estado",
        "id_proceso_disciplinario",
        "id_etapa",
        "created_user",
        "updated_user",
        "deleted_user",
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

    public function dependencia() {
        return $this->belongsTo(DependenciaOrigenModel::class,"id_dependencia");
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
