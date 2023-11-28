<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoProcesoDisciplinarioModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_tipo_proceso_disciplinario";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "nombre",
        "observacion",
        "estado"
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'number';
    public $incrementing = false;
}
