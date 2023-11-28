<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempInteresadosModel extends Model
{
    use HasFactory;

    protected $table = "temp_interesados";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_temp_proceso_disciplinario",
        "id_etapa",
        "id_tipo_interesado",
        "estado",
        "id_funcionario",
        "tipo_documento",
        "numero_documento",
        "primer_nombre",
        "segundo_nombre",
        "direccion",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_user",
        "updated_user",
        "deleted_user",
        "created_at",
        "updated_at",
    ];
}