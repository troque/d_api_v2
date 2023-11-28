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
        "tipo_interesado",
        "tipo_sujeto_procesal",
        "primer_nombre",
        "segundo_nombre",
        "primer_apellido",
        "segundo_apellido",
        "tipo_documento",
        "numero_documento",
        "email",
        "telefono",
        "telefono2",
        "cargo",
        "orientacion_sexual",
        "sexo",
        "direccion",
        "departamento",
        "ciudad",
        "localidad",
        "entidad",
        "sector",
        "radicado",
        "vigencia",
        "item",
    ];

    protected $hidden = [
        "created_user",
        "updated_user",
        "deleted_user",
        "created_at",
        "updated_at",
    ];
}
