<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluacionoExpedientePermitidoModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "evaluacion_expediente_permitido";

    public $timestamps = true;

    protected $fillable = [
        "TIPO_EXPEDIENTE_ID",
        "FASE_ID",
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

    protected $primaryKey = 'TIPO_EXPEDIENTE_ID';
    protected $keyType = 'number';
    public $incrementing = false;
}
