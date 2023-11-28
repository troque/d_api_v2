<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RemisionQuejaModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "remision_queja";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_proceso_disciplinario",
        "id_tipo_evaluacion",
        "id_dependencia_origen",
        "id_dependencia_destino",
        "created_user",
        "updated_user",
        "deleted_user",
        "eliminado",
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

    public function dependencia_origen() {
        return $this->belongsTo(DependenciaOrigenModel::class,"id_dependencia_origen");
    }

    public function dependencia_destino() {
        return $this->belongsTo(DependenciaOrigenModel::class,"id_dependencia_destino");
    }

    public function getTipoEvaluacion($id_evaluacion){
        $evaluacion = ResultadoEvaluacionModel::where([
            ['id', '=', $id_evaluacion]
        ])->get();
        return $evaluacion[0]->nombre;
    }

    public function getUsuarioJefeDependencia($id_jefe){
        $usuario_jefe = User::where([
            ['id', '=', $id_jefe]
        ])->get();

        if(count($usuario_jefe) <= 0){
            $resultado['estado'] = false;
            $resultado['nombre'] = "LA DEPENDENCIA NO TIENE USUARIO JEFE ASIGNADO";
            return $resultado;
        }

        if(!$usuario_jefe[0]->reparto_habilitado){
            $resultado['estado'] = false;
            $resultado['nombre'] = "USUARIO NO ACTIVO PARA REPARTO";
            return $resultado;
        }

        if(!$usuario_jefe[0]->estado){
            $resultado['estado'] = false;
            $resultado['nombre'] = "USUARIO NO ACTIVO";
            return $resultado;
        }

        $resultado['estado'] = true;
        $resultado['nombre'] = $usuario_jefe[0]->nombre . ' ' . $usuario_jefe[0]->apellido;
        return $resultado;
    }

    public function getIncorporacion() {
        return $this->hasOne(IncorporacionModel::class,"id_proceso_disciplinario_incorporado", "id_proceso_disciplinario");
    }

}
