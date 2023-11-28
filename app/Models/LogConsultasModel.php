<?php

namespace App\Models;

use App\Http\Utilidades\Constants;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogConsultasModel extends Model
{

    protected $table = "log_consultas";

    public $timestamps = true;

    protected $fillable = [
        "id_usuario",
        "id_proceso_disciplinario",
        "filtros",
        "resultados_busqueda",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'number';
    public $incrementing = false;


    public function usuario(){
        return $this->hasOne(User::class, "id", "id_usuario");
    }

    public function proceso_disciplinario(){
        return $this->hasOne(ProcesoDiciplinarioModel::class, "uuid", "id_proceso_disciplinario");
    }
}
