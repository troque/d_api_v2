<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformeCierreModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "informe_cierre";
    public $timestamps = true;

    protected $fillable = [
        "id_proceso_disciplinario",
        "descripcion",
        "radicado_sirius",
        "documento_sirius",
        "id_documento_sirius",
        "id_fase",
        "id_etapa",
        "id_dependencia",
        "finalizado",
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

    public function etapa(){
        return $this->hasOne(EtapaModel::class, "id", "id_etapa");
    }

    public function documentoSoportes(){
        return $this->hasOne(DocumentoSiriusModel::class, "uuid", "id_documento_sirius");
    }

    public function dependencia(){
        return $this->hasOne(DependenciaOrigenModel::class, "id", "id_dependencia");
    }
}
