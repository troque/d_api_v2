<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoSujetoProcesalModel;
use App\Models\TipoInteresadoModel;
use App\Models\TipoEntidadModel;
use App\Models\Traits\HasUuid;

class PortalConfiguracionTipoInteresadoModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "portal_configuracion_tipo_interesado";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_tipo_sujeto_procesal",
        "permiso_consulta",
        "estado",
        "created_user",
        "updated_user",
        "delete_user",
        "created_at",
        "updated_at",
        "id_tipo_interesado"
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

    public function tipo_sujeto_procesal()
    {
        // Se valida cuando el tipo de interesado es Persona Natural
        if ($this['id_tipo_interesado'] == 1) {

            // Se retorna la informacion
            return $this->belongsTo(TipoSujetoProcesalModel::class, "id_tipo_sujeto_procesal", "id");
        }

        // Se valida cuando el tipo de interesado es Entidad
        if ($this['id_tipo_interesado'] == 2) {

            // Se retorna la informacion
            return $this->belongsTo(TipoEntidadModel::class, "id_tipo_sujeto_procesal", "id");
        }
    }

    public function tipo_interesado()
    {
        // Se retorna la informacion
        return $this->belongsTo(TipoInteresadoModel::class, "id_tipo_interesado", "id");
    }
}
