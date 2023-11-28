<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentoCierreModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "documento_cierre";

    public $timestamps = true;

    protected $fillable = [
        "id_proceso_disciplinario",
        "estado",
        "seguimiento",
        "descripcion_seguimiento",
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

}
