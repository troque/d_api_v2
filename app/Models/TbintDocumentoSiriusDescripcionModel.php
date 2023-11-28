<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TbintDocumentoSiriusDescripcionModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "tbint_documento_sirius_descripcion";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_proceso_disciplinario",
        "descripcion",
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

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

}
