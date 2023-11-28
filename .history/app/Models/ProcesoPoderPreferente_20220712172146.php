<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcesoSinprocModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "proceso_poder_prferente";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_tramite_usuario",
        "fecha_ingreso",
        "id_proceso_disciplinario",
        // "numero_auto",
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

    public function dependencia_origen() {
        return $this->belongsTo(DependenciaOrigenModel::class,"id_dependencia_origen");
    }

}
