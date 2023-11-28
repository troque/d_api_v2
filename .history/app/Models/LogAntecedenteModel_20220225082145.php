<?php

namespace App\Models;

use App\Models\AntecedenteModel as AntecedenteModel;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogEtapaModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "log_antecedente";

    public $timestamps = true;

    protected $fillable = [
        "id_proceso_disciplinario",
        "id_etapa",
        "id_fase",
        "id_tipo_cambio",
        "id_estado",
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


    public function etapa() {
        return $this->belongsTo(EtapaModel::class,"id_etapa");
    }

}
