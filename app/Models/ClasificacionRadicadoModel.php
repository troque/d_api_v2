<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasUuid;

class ClasificacionRadicadoModel extends Model
{
    use HasFactory, HasUuid;
    use SoftDeletes;

    protected $table = "clasificacion_radicado";

    public $timestamps = true;

    protected $fillable = [
        "uuid",
        "id_proceso_disciplinario",
        "id_etapa",
        "id_tipo_expediente",
        "observaciones",
        "id_tipo_queja",
        "id_termino_respuesta",
        "fecha_termino",
        "hora_termino",
        "gestion_juridica",
        "estado",
        "id_estado_reparto",
        "id_tipo_derecho_peticion",
        "oficina_control_interno",
        "created_user",
        "updated_user",
        "deleted_user",
        "reclasificacion",
        "id_dependencia",
        "validacion_jefe",
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


    /**
     * Una clasificacion del radicado pertenece a un tipo de expediente, por eso la relación BelongsTo.
     */
    public function expediente()
    {
        return $this->belongsTo(TipoExpedienteModel::class, "id_tipo_expediente");
    }

    /**
     * Una clasificacion del radicado pertenece a un tipo de expediente, por eso la relación BelongsTo.
     */
    public function tipo_queja()
    {
        return $this->belongsTo(TipoQuejaModel::class, "id_tipo_queja");
    }

    /**
     *Una clasificacion del radicado pertenece a un tipo de derecho de peticion, por eso la relación BelongsTo.
     */
    public function tipo_derecho_peticion()
    {
        return $this->belongsTo(TipoDerechoPeticionModel::class, "id_tipo_derecho_peticion");
    }

    public function usuarioRegistra()
    {
        return $this->belongsTo(User::class, "created_user", "name");
    }

    public function procesoDisciplinario()
    {
        return $this->belongsTo(ProcesoDiciplinarioModel::class, "id_proceso_disciplinario");
    }

    public function etapa()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa");
    }

    public function fases_permitidas()
    {
        return $this->belongsToMany(FaseModel::class, 'EVALUACION_EXPEDIENTE_PERMITIDO', 'TIPO_EXPEDIENTE_ID', 'fase_id', 'id_tipo_expediente');
    }

    public function dependencia()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "id_dependencia");
    }

    public function mensaje_de_terminos()
    {

        if ($this['id_tipo_expediente'] == 1) { // Derecho de petición

            $query = TipoExpedienteMensajesModel::where([
                ['id_tipo_expediente', '=', $this['id_tipo_expediente']],
                ['id_sub_tipo_expediente', '=', $this['id_tipo_derecho_peticion']]

            ])->take(1)->first();

            if ($query != null)
                return $query['mensaje'];
            else {
                return '';
            }
        } else if ($this['id_tipo_expediente'] == 2) { // Poder referente a solicitud

            $query = TipoExpedienteMensajesModel::where([
                ['id_tipo_expediente', '=', $this['id_tipo_expediente']],
                ['id_sub_tipo_expediente', '=', $this['id_tipo_queja']]

            ])->take(1)->first();

            if ($query != null)
                return $query['mensaje'];
            else {
                return '';
            }
        } else if ($this['id_tipo_expediente'] == 3) { // Queja

            $query = TipoExpedienteMensajesModel::where([
                ['id_tipo_expediente', '=', $this['id_tipo_expediente']],
                ['id_sub_tipo_expediente', '=', $this['id_tipo_queja']]

            ])->take(1)->first();

            if ($query != null)
                return $query['mensaje'];
            else {
                return '';
            }
        } else if ($this['id_tipo_expediente'] == 4) { // Tutela

            $query = TipoExpedienteMensajesModel::where([
                ['id_tipo_expediente', '=', $this['id_tipo_expediente']],
                ['id_sub_tipo_expediente', '=', $this['id_termino_respuesta']]

            ])->take(1)->first();

            if ($query != null)
                return $query['mensaje'];
            else {
                return '';
            }
        }
    }

    /**
     * Obtener descripción corta de un proceso disciplinario. Máx 200 carácteres.
     * @return String
     */
    public function getObservacionCorta()
    {

        if (strlen($this['observaciones']) >= 200) {

            return substr($this['observaciones'], 0, 200);
        }

        return $this['observaciones'];
    }


    /**
     *
     */
    public function log()
    {

        return $this->hasOne(LogProcesoDisciplinarioModel::class, "id_fase_registro", "uuid");
    }
}
