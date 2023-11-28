<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Modelado de un antecedente
 * @autor: Sandra Saavedra
 * @Fecha: 27 diciembre 2021
 */
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
        "created_at",
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


    /**
     * Relación con EtapaModel
     * @return EtapaModel
     */
    public function etapa()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa");
    }

    /**
     * Relación con el User
     * @return User
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, "created_user", "name");
    }

    /**
     * Relación con DependenciaOrigenModel
     * @return DependenciaOrigenModel
     */
    public function dependencia()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "id_dependencia");
    }


    /**
     * Obtener descripción corta de un proceso disciplinario. Máx 50 carácteres.
     * @return String
     */
    public function getDescripcionCorta()
    {

        if (strlen($this['descripcion']) >= 200) {

            return substr($this['descripcion'], 0, 200);
        }

        return $this['descripcion'];
    }
}
