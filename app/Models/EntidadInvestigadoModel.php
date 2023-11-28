<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuid;

class EntidadInvestigadoModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "entidad_investigado";

    public $timestamps = true;

    protected $fillable = [
        "id_proceso_disciplinario",
        "id_etapa",
        "id_entidad",
        "nombre_investigado",
        "cargo",
        "codigo",
        "observaciones",
        "estado",
        "requiere_registro",
        "created_user",
        "updated_user",
        "deleted_user",
        "investigado",
        "contratista",
        "planta",
        "comentario_identifica_investigado",
        "id_sector",
        "created_at",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "updated_user",
        "deleted_user",
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, "created_user", "name");
    }

    public function getDependenciaUsuario($dependenciaId)
    {
        $dependencia = DependenciaOrigenModel::where([
            ['id', '=', $dependenciaId]
        ])->get();
        return $dependencia[0]->nombre;
    }

    /**
     *
     */
    public function etapa()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa");
    }


    public function getObservacionCorta()
    {

        if (strlen($this['observaciones']) >= 200) {

            return substr($this['observaciones'], 0, 200);
        }

        return $this['observaciones'];
    }
}
