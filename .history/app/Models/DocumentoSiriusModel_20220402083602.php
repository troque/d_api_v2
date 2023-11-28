<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuid;

class DocumentoSiriusModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "documento_sirius";

    public $timestamps = true;

    protected $fillable = [
        "id_proceso_disciplinario",
        "id_etapa",
        "id_fase",
        "url_archivo",
        "nombre_archivo",
        "estado",
        "num_folios",
        "num_radicado",
        "extension",
        "peso",
        "grupo",
        "es_compulsa",
        "path",
        "id_mas_formato",
        "id_log_proceso_disciplinario"
        "es_soporte",
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


     /**
     * Una clasificacion del radicado pertenece a un tipo de expediente, por eso la relación BelongsTo.
     */
    public function etapa() {
        return $this->belongsTo(EtapaModel::class,"id_etapa");
    }

    /**
     * Una clasificacion del radicado pertenece a un tipo de expediente, por eso la relación BelongsTo.
     */
    public function fase() {
        return $this->belongsTo(FaseModel::class,"id_fase");
    }

    /**
     * Una clasificacion del radicado pertenece a un tipo de expediente, por eso la relación BelongsTo.
     */
    public function compulsa() {
        return $this->hasOne(CompulsaModel::class,"id_documento_sirius", $this->primaryKey);
    }

    public function usuario() {
        return $this->belongsTo(User::class,"created_user","name");
    }

    /**
     * Obtener la descripcion del documento
     */
    public function descripcion() {
        return $this->hasOne(TbintDocumentoSiriusDescripcionModel::class, "UUID", "grupo");
    }
}
