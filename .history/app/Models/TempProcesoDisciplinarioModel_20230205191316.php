<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempProcesoDisciplinarioModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "temp_proceso_disciplinario";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "radicado",
        "id_etapa",
        "vigencia",
        "estado",
        "id_tipo_proceso",
        "id_dependencia_origen",
        "id_dependencia_duena",
        "id_tipo_expediente",
        "id_sub_tipo_expediente",
        "id_tipo_evaluacion",
        "id_tipo_conducta",
        "radicado_padre_desglose",
        "vigencia_padre_desglose",
        "auto_desglose",
        "created_user",
        "updated_user",
        "deleted_user",
        "usuario_actual"
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

    public function idVigencia()
    {
        return $this->belongsTo(VigenciaModel::class, "vigencia", "vigencia");
    }

    public function etapa()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa");
    }

    public function dependenciaOrigen()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "id_dependencia_origen");
    }

    public function dependenciaDuena()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "id_dependencia_duena");
    }

    public function tipoProceso()
    {
        return $this->belongsTo(TipoProcesoModel::class, "id_tipo_proceso");
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, "created_user", "name");
    }

    public function usuarioActual()
    {
        return $this->belongsTo(User::class, "usuario_actual", "name");
    }
}
