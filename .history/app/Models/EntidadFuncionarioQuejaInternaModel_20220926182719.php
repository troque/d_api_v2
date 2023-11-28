<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoRespuestaModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "entidad_funcionario_queja_interna";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "id_entidad_investigado",
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
        "created_user",
        "updated_user",
        "deleted_user",


        "data.attributes.id_entidad_investigado" => ["required"],
            "data.attributes.id_tipo_funcionario" => ["required"],
            "data.attributes.id_tipo_documento" => ["required"],
            "data.attributes.numero_documento" => ["required"],
            "data.attributes.primer_nombre" => ["required"],
            "data.attributes.segundo_nombre" => [""],
            "data.attributes.primer_apellido" => ["required"],
            "data.attributes.segundo_apellido" => [""],
            "data.attributes.razon_social" => [""],
            "data.attributes.numero_contrato" => [""],
            "data.attributes.dependencia" => [""],
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
