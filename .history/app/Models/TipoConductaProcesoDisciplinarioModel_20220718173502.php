<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoConductaProcesoDisciplinarioModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_tipo_transaccion";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_proceso_disciplinario",
        "id_tipo_conducta",
        "estado",
        "id_etapa",
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
