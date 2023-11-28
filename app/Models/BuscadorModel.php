<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MasEstadoActuaciones;
use App\Models\MasActuacionesModel;

class BuscadorModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "log_proceso_disciplinario";

    public $timestamps = true;

    protected $fillable = [

    ];

    protected $hidden = [
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    public function log_proceso_disciplinario()
    {
        return $this->belongsTo(LogProcesoDisciplinarioModel::class, "uuid" , "uuid");
    }

    public function etapa()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa", "id");
    }

    public function dependenciaOrigen()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "id_dependencia_origen", "id");
    }
}