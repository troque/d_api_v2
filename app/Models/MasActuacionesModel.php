<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasActuacionesModel extends Model
{
    use HasFactory;

    protected $table = "mas_actuaciones";

    public $timestamps = true;

    protected $fillable = [
        "nombre_actuacion",
        "nombre_plantilla",
        "id_etapa",
        "estado",
        "id_etapa_despues_aprobacion",
        "despues_aprobacion_listar_actuacion",
        "generar_auto",
        "created_user",
        "updated_user",
        "deleted_user",
        "nombre_plantilla_manual",
        "texto_dejar_en_mis_pendientes",
        "texto_enviar_a_alguien_de_mi_dependencia",
        "texto_enviar_a_jefe_de_la_dependencia",
        "texto_enviar_a_otra_dependencia",
        "texto_regresar_proceso_al_ultimo_usuario",
        "texto_enviar_a_alguien_de_secretaria_comun_dirigido",
        "texto_enviar_a_alguien_de_secretaria_comun_aleatorio",
        "tipo_actuacion",
        "excluyente",
        "cierra_proceso",
        "visible",
        "campos",
        "etapa_siguiente"
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "updated_user",
        "deleted_user",
    ];

    public function etapa()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa");
    }

    public function etapa_despues_aprobacion()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa_despues_aprobacion", "id");
    }
}
