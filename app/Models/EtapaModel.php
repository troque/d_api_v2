<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EtapaModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_etapa";

    public $timestamps = true;

    protected $fillable = [
        "nombre",
        "estado",
        "id_tipo_proceso",
        "created_user",
        "updated_user",
        "deleted_user",
        "estado_poder_preferente",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    public function tipo_proceso()
    {
        return $this->belongsTo(TipoProcesoModel::class, "id_tipo_proceso");
    }
}
