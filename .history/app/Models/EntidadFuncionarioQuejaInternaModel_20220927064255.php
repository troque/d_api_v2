<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntidadFuncionarioQuejaInternaModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "entidad_funcionario_queja_interna";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "se_identifica_investigado",
        "id_proceso_disciplinario",
        "id_tipo_funcionario",
        "id_tipo_documento",
        "numero_documento",
        "primer_nombre",
        "segundo_nombre",
        "primer_apellido",
        "segundo_nombre",
        "razon_social",
        "numero_contrato",
        "dependencia",
        "observaciones",
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
}
