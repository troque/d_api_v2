<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GestorRespuestaModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "Gestor_Respuesta";

    public $timestamps = true;

    protected $fillable = [
        "id_proceso_disciplinario",
        "version",
        "nuevo_documento",
        "aprobado",
        "orden_funcionario",
        "descripcion",
        "proceso_finalizado",
        "id_mas_orden_funcionario",
        "id_documento_sirius",
        "created_user",
        "updated_user",
        "deleted_user",
        "eliminado",
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

    public function clasificacionExpediente() {
        return $this->hasOne(ClasificacionRadicadoModel::class,"id_proceso_disciplinario", "id_proceso_disciplinario")->latest('created_at');
    }

    public function usuario(){
        return $this->hasOne(User::class, "name", "created_user");
    }

    public function documentoSirius(){
        return $this->hasOne(DocumentoSiriusModel::class, "uuid", "id_documento_sirius");
    }

}
