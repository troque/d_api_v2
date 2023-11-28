<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProcesoDisciplinarioPorSemaforoModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "proceso_disciplinario_por_semaforo";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "id_semaforo",
        "id_proceso_disciplinario",
        "id_actuacion",
        "fecha_inicio",
        "estado",
        "created_user",
        "updated_user",
        "deleted_user",
        "finalizo",
        "fechafinalizo",
        "id_actuacion_finaliza",
        "id_dependencia_finaliza",
        "id_usuario_finaliza"
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    public function get_id_semaforo() {
        return $this->belongsTo(SemaforoModel::class,"id_semaforo","id");
    }

    public function get_id_proceso_disciplinario() {
        return $this->belongsTo(ProcesoDiciplinarioModel::class,"id_proceso_disciplinario","uuid");
    }

    public function get_id_actuacion() {
        return $this->belongsTo(ActuacionesModel::class,"id_actuacion","uuid");
    }

    public function get_condiciones() {
        return $this->hasMany(CondicionModel::class,"id_semaforo","id_semaforo");
    }

    public function get_actuacion($id_actuacion) {
        $actuacion = MasActuacionesModel::where("id", $id_actuacion)->get();
        if(count($actuacion) > 0){
           return $actuacion[0];
        }
        return null;
    }

    public function motivo_finalizado($id_actuacion_finaliza, $id_dependencia_finaliza, $id_usuario_finaliza){
        if($id_actuacion_finaliza){
            $actuacion = DB::select(
                "
                    SELECT
                        ma.nombre_actuacion
                    FROM
                    mas_actuaciones ma
                    INNER JOIN actuaciones a ON ma.id = a.id_actuacion
                    WHERE a.uuid = '$id_actuacion_finaliza'
                "
            );

            if(count($actuacion) > 0){
                return "FINALIZO POR LA ACTUACIÓN: " . $actuacion[0]->nombre_actuacion;
            }
        }
        else if($id_dependencia_finaliza){
            $dependencia = DB::select(
                "
                    SELECT
                        mdo.nombre
                    FROM
                    mas_dependencia_origen mdo
                    WHERE mdo.id = $id_dependencia_finaliza
                "
            );

            if(count($dependencia) > 0){
                return "FINALIZÓ CUANDO SE HIZO TRANSFERENCIA A LA DEPENDENCIA: " . $dependencia[0]->nombre;
            }
        }
        else if($id_usuario_finaliza){
            $usuario = DB::select(
                "
                    SELECT
                        u.nombre,
                        u.apellido
                    FROM
                    users u
                    WHERE u.id = $id_usuario_finaliza
                "
            );

            if(count($usuario) > 0){
                return "FINALIZÓ CUANDO SE HIZO TRANSFERENCIA AL GRUPO DE TRABAJO";
            }
        }

        return null;
    }
}
