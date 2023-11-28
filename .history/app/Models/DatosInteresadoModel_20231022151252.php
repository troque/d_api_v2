<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TipoInteresadoModel;
use App\Models\TipoSujetoProcesalModel;


class DatosInteresadoModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "interesado";

    public $timestamps = true;

    protected $fillable = [
        "id_etapa",
        "id_tipo_interesao",
        "id_tipo_sujeto_procesal",
        "tipo_documento",
        "numero_documento",
        "primer_nombre",
        "segundo_nombre",
        "primer_apellido",
        "segundo_apellido",
        "id_departamento",
        "id_ciudad",
        "direccion",
        "direccion_json",
        "id_localidad",
        "email",
        "telefono_celular",
        "telefono_fijo",
        "id_sexo",
        "id_genero",
        "id_orientacion_sexual",
        "entidad",
        "cargo",
        "cargo_descripcion",
        "tarjeta_profesional",
        "id_dependencia",
        "id_dependencia_entidad",
        "id_tipo_entidad",
        "nombre_entidad",
        "id_entidad",
        "id_funcionario",
        "id_proceso_disciplinario",
        "estado",
        "folio",
        "created_user",
        "updated_user",
        "deleted_user",
        "autorizar_envio_correo"

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



    public function getCiudad()
    {
        $ciudad = CiudadModel::where([
            ['id', '=', $this['id_ciudad']],
            ['id_departamento', '=', $this['id_departamento']]

        ])->take(1)->first();

        if ($ciudad != null)
            return $ciudad['nombre'];
        else {
            return '';
        }
    }

    public function getDepartamento()
    {
        $departamento = DepartamentoModel::where([
            ['id', '=', $this['id_departamento']]
        ])->take(1)->first();
        if ($departamento != null)
            return $departamento['nombre'];
        else {
            return '';
        }
    }


    public function etapa()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa");
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, "created_user", "name");
    }


    public function dependencia()
    {
        if ($this->id_dependencia != null) {
            return $this->belongsTo(DependenciaOrigenModel::class, "id_dependencia");
        } else {
            return null;
        }
    }


    public function tipo_interesado()
    {
        return $this->belongsTo(TipoInteresadoModel::class, "id_tipo_interesao", "id");
    }

    public function sujeto_procesal()
    {
        return $this->belongsTo(TipoSujetoProcesalModel::class, "id_tipo_sujeto_procesal", "id");
    }
}
