<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcesoDesgloseModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "proceso_desglose";

    public $timestamps = true;

    protected $fillable = [
        "id_tramite_usuario",
        "fecha_ingreso",
        "numero_auto",
        "auto_asociado",
        "fecha_auto_desglose",
        "id_dependencia_origen",
        "observacion_mesa_trabajo",
        "id_proceso_disciplinario",
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

}
