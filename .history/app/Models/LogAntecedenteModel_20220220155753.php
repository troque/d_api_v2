<?php

namespace App\Models;

use App\Models\AntecedenteModel as AntecedenteModel;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogAntecedenteModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "antecedente";

    public $timestamps = true;

    protected $fillable = [
        "id_antecedente",
        "observacion_estado",
        "descripcion",
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

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;


    public function antecendete() {
        return $this->belongsTo(AntecedenteModel::class,"id_antecedente");
    }

    public function dependencia() {
        return $this->belongsTo(DependenciaOrigenModel::class,"id_dependencia");
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
