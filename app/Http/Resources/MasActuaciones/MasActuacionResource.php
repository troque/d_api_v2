<?php

namespace App\Http\Resources\MasActuaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class MasActuacionResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
   */
  public function toArray($request)
  {
    return [
      "type" => "mas_actuaciones",
      "id" => (string) $this->resource->getRouteKey(),
      "attributes" => [
        "nombre_actuacion" => $this->resource->nombre_actuacion,
        "nombre_plantilla" => $this->resource->nombre_plantilla,
        "id_etapa" => $this->resource->id_etapa,
        "nombre_etapa" => isset($this->resource->etapa->nombre) ? $this->resource->etapa->nombre : "",
        //"id_etapa_despues_aprobacion" => $this->resource->id_etapa_despues_aprobacion,
        "etapa_siguiente" => $this->resource->etapa_siguiente,
        "nombre_etapa_despues_aprobacion" => $this->resource->etapa_despues_aprobacion != null ? $this->resource->etapa_despues_aprobacion->nombre : "",
        "despues_aprobacion_listar_actuacion" => $this->resource->despues_aprobacion_listar_actuacion,
        "generar_auto" => $this->resource->generar_auto,
        "estado" => $this->resource->estado,
        "nombre_plantilla_manual" => $this->resource->nombre_plantilla_manual != null ? $this->resource->nombre_plantilla_manual : "",
        "texto_dejar_en_mis_pendientes" => $this->resource->texto_dejar_en_mis_pendientes != null ? $this->resource->texto_dejar_en_mis_pendientes : "",
        "texto_enviar_a_alguien_de_mi_dependencia" => $this->resource->texto_enviar_a_alguien_de_mi_dependencia != null ? $this->resource->texto_enviar_a_alguien_de_mi_dependencia : "",
        "texto_enviar_a_jefe_de_la_dependencia" => $this->resource->texto_enviar_a_jefe_de_la_dependencia != null ? $this->resource->texto_enviar_a_jefe_de_la_dependencia : "",
        "texto_enviar_a_otra_dependencia" => $this->resource->texto_enviar_a_otra_dependencia != null ? $this->resource->texto_enviar_a_otra_dependencia : "",
        "texto_regresar_proceso_al_ultimo_usuario" => $this->resource->texto_regresar_proceso_al_ultimo_usuario != null ? $this->resource->texto_regresar_proceso_al_ultimo_usuario : "",
        "texto_enviar_a_alguien_de_secretaria_comun_dirigido" => $this->resource->texto_enviar_a_alguien_de_secretaria_comun_dirigido != null ? $this->resource->texto_enviar_a_alguien_de_secretaria_comun_dirigido : "",
        "texto_enviar_a_alguien_de_secretaria_comun_aleatorio" => $this->resource->texto_enviar_a_alguien_de_secretaria_comun_aleatorio != null ? $this->resource->texto_enviar_a_alguien_de_secretaria_comun_aleatorio : "",
        "tipo_actuacion" => $this->resource->tipo_actuacion != null ? $this->resource->tipo_actuacion : "",
        "excluyente" => $this->resource->excluyente != null ? $this->resource->excluyente : "",
        "cierra_proceso" => $this->resource->cierra_proceso != null ? $this->resource->cierra_proceso : "",
        "visible" => $this->resource->visible != null ? $this->resource->visible : "",
        "campos" => $this->resource->campos != null ? json_decode($this->resource->campos) : ""
      ],
      "links" => [
        "self" => url(route("api.v1.mas_actuaciones.show", $this->resource)),
      ],
    ];
  }
}
