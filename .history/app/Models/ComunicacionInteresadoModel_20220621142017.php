<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuid;

class ComunicacionInteresadoModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "comunicacion_interesado";

    public $timestamps = true;

    protected $fillable = [
        "id_interesado",
        "id_documento_sirius",
        'id_proceso_disciplinario',
        "estado",
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
     * Obtiene la informacion de un documento sirius
     */
    public function documento() {
        return $this->hasOne(DocumentoSiriusModel::class, "UUID", "id_documento_sirius");
    }
}
