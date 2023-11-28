<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActuacionInactivaModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "actuaciones_inactivas";

    public $timestamps = true;

    protected $fillable = [
        "id_actuacion",
        "id_actuacion_principal",
        "id_proceso_disciplinario",
        "created_user"
    ];

    protected $hidden = [
        "created_at",
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

}
