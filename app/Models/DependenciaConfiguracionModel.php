<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class DependenciaConfiguracionModel extends Model
{

    use HasFactory;
    use SoftDeletes;


    protected $table = "mas_dependencia_configuracion";



    protected $fillable = [
        "id_dependencia_origen",
        "id_dependencia_acceso",
        "porcentaje_asignacion",
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

    protected $primaryKey = 'id_dependencia_origen';
    protected $keyType = 'number';
    public $incrementing = false;

}
