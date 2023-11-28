<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompulsaModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "compulsa";

    public $timestamps = true;

    protected $fillable = [
        "id_proceso_disciplinario",
        "radicado",
        "vigencia",
        "id_proceso_disciplinario_compulsa",
        "radicado_compulsa",
        "vigencia_compulsa",
        "id_documento_sirius",
        "id_documento_sirius_compulsa",
        "created_user",
        "updated_user",
        "deleted_user"
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
