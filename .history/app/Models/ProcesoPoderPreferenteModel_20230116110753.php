<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcesoPoderPreferenteModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "proceso_poder_preferente";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_tramite_usuario",
        "fecha_ingreso",
        "id_proceso_disciplinario",
        "entidad_involucrada",
        "dependencia_cargo",
        "id_etapa_involucrada",
        // "auto_sociado",
        // "fecha_auto_desgloce",
        // "id_dependencia_origen",
        // "observacion_mesa_trabajo",
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

    public function dependencia_cargo()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "dependencia_cargo");
    }

    public function entidad_involucrada()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "dependencia_cargo");
    }
}
