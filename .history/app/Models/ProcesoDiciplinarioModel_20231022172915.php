<?php

namespace App\Models;

use App\Http\Utilidades\Constants;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use DateTime;

class ProcesoDiciplinarioModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "proceso_disciplinario";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_tipo_proceso",
        "id_tipo_radicado",
        "id_origen_radicado",
        "radicado",
        "vigencia",
        "estado",
        "id_dependencia",
        "created_user",
        "updated_user",
        "deleted_user",
        "id_etapa",
        "id_funcionario_asignado",
        "usuario_comisionado",
        "temporal_usuario_comisionado",
        "id_dependencia",
        "id_dependencia_duena",
        "radicado_padre",
        "vigencia_padre",
        "vigencia_origen",
        "tipo_radicacion",
        "id_dependencia_actual",
        "migrado",
        "fuente_bd",
        "fuente_excel",
        "created_at",
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


    public function etapa()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa");
    }

    public function tipoProceso()
    {
        return $this->belongsTo(TipoProcesoModel::class, "id_tipo_proceso");
    }

    public function origenRadicado()
    {
        return $this->belongsTo(OrigenRadicadoModel::class, "id_origen_radicado");
    }

    public function proceso_sinproc()
    {
        return $this->hasOne(ProcesoSinprocModel::class, "id_proceso_disciplinario");
    }

    public function proceso_sirius()
    {
        return $this->hasOne(ProcesoSiriusModel::class, "id_proceso_disciplinario");
    }

    public function proceso_desglose()
    {
        return $this->hasOne(ProcesoDesgloseModel::class, "id_proceso_disciplinario");
    }

    public function proceso_poder_preferente()
    {
        return $this->hasOne(ProcesoPoderPreferenteModel::class, "id_proceso_disciplinario");
    }


    /*public function radicado()
    {

        if ($this->id_tipo_proceso == 1) {
            return substr($this->radicado, 8, strlen($this->radicado));
        }

        return $this->radicado;
    }*/

    public function antecedente()
    {

        return $this->hasOne(AntecedenteModel::class, "id_proceso_disciplinario")->latest('created_at');
    }

    public function ultima_clasificacion()
    {

        return $this->hasOne(ClasificacionRadicadoModel::class, "id_proceso_disciplinario")->latest('created_at');
    }

    public function log_etapa()
    {
        return $this->hasOne(LogProcesoDisciplinarioModel::class, "id_proceso_disciplinario")->latest('created_at');
    }

    public function documentos_sirius()
    {
        return $this->hasMany(DocumentoSiriusModel::class, "id_proceso_disciplinario");
    }

    public function getDiasCalendario()
    {
        $createDate = new DateTime($this['created_at']);
        $createDate->setTime(0, 0, 0);
        $nowDate = new DateTime("now");
        $nowDate->setTime(0, 0, 0);
        return $createDate->diff($nowDate)->days;
    }

    public function getDiasHabiles()
    {
        $createDate = new DateTime($this['created_at']);
        $createDate->setTime(0, 0, 0);
        $nowDate = new DateTime("now");
        $nowDate->setTime(0, 0, 0);
        $daysDiff = $createDate->diff($nowDate)->days;
        $daysNoLaborables = DiasNoLaboralesModel::where([
            ['fecha', '>=', $createDate],
            ['fecha', '<=', $nowDate],
            ['estado', '=', 1]
        ])->count();
        return $daysDiff - $daysNoLaborables;
    }

    public function getUsuarioRegistro()
    {
        return $this->hasOne(User::class, "name", "created_user");
    }

    public function getUsuarioComisionado()
    {
        return $this->hasOne(User::class, "id", "usuario_comisionado");
    }

    public function getTipoExpediente()
    {

        return $this->hasOne(ClasificacionRadicadoModel::class, "id_proceso_disciplinario", "uuid")->latest('created_at');
    }

    public function getTipoEvaluacion($proceso_disiciplinario)
    { //TEMPORAL - SE DEBE IMPLEMENTAR EN EL LOG

        $resultado_evaluacion = EvaluacionModel::where(
            "id_proceso_disciplinario",
            $proceso_disiciplinario
        )->where('eliminado',  Constants::ESTADOS_ELIMINADO['no_eliminado'])->orderBy('created_at', 'desc')->first();

        if ($resultado_evaluacion) {
            $resultado_evaluacion = ResultadoEvaluacionModel::where(
                "id",
                $resultado_evaluacion->resultado_evaluacion
            )->first();

            return $resultado_evaluacion;
        } else {
            return null;
        }
    }

    public function getEstado()
    {
        return $this->hasOne(MasEstadoProcesoDisciplinarioModel::class, "id", "estado");
    }

    public function getDependenciaDuena()
    {
        return $this->hasOne(DependenciaOrigenModel::class, "id", "id_dependencia_duena");
    }

    public function getDependenciaActual()
    {
        return $this->hasOne(DependenciaOrigenModel::class, "id", "id_dependencia_actual");
    }

    public function getEtapa()
    {
        return $this->hasOne(EtapaModel::class, "id", "id_etapa");
    }

    public function getUltimaTranspasoAccion($id_proceso_disciplinario)
    {
        $log = LogProcesoDisciplinarioModel::where('id_proceso_disciplinario', $id_proceso_disciplinario)
            ->whereColumn('id_funcionario_registra', '<>', 'id_funcionario_asignado')
            ->orderByDesc('created_at')->get();

        if (count($log) > 0) {
            $dependencia = DependenciaOrigenModel::where('id', $log[0]->id_dependencia_origen)->get();
            $usuario = User::where('name', $log[0]->id_funcionario_registra)->get();

            $datos['fecha_transaccion'] = date("d/m/Y h:i:s A", strtotime($log[0]->created_at));
            $datos['observacion'] = $log[0]->descripcion;
            $datos['usuario'] = $usuario[0]->nombre . " " . $usuario[0]->apellido;
            $datos['dependencia_remitente'] = $dependencia[0]->nombre;
        } else {
            $datos['fecha_transaccion'] = 'SIN DATOS';
            $datos['observacion'] = 'SIN DATOS';
            $datos['usuario'] = 'SIN DATOS';
            $datos['dependencia_remitente'] = 'SIN DATOS';
        }

        return $datos;
    }

    public function getUsuarioActual($uuid)
    {
        $log = LogProcesoDisciplinarioModel::where("id_proceso_disciplinario", $uuid)->orderByDesc('created_at')->get();
        if (count($log) <= 0) {
            return null;
        }
        $user = User::where("name", $log[0]->id_funcionario_actual)->get();
        if (count($user) <= 0) {
            return null;
        }
        $dependencia = DependenciaOrigenModel::where("id", $user[0]->id_dependencia)->get();
        if (count($dependencia) <= 0) {
            return null;
        }
        $usuario['nombre'] = $user[0]->nombre;
        $usuario['apellido'] = $user[0]->apellido;
        $usuario['dependencia'] = $dependencia[0]->nombre;
        return $usuario;
    }
}
