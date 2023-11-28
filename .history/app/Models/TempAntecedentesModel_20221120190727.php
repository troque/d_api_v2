<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempAntecedentesModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "temp_antecedentes";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_temp_proceso_disciplinario",
        "descripcion",
        "fecha_registro",
        "estado",
        "id_etapa",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_user",
        "updated_user",
        "deleted_user",
        "created_at",
        "updated_at",
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    public function tempDisciplinario()
    {
        return $this->belongsTo(TempProcesoDisciplinarioModel::class, "id_temp_proceso_disciplinario");
    }
}
