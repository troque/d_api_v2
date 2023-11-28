<?php

namespace App\Http\Resources\Actuaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class ActuacionesResource extends JsonResource
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
            "type" => "actuaciones",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_actuacion" => $this->resource->id_actuacion,
                "usuario_accion" => empty($this->resource->usuario_accion) ? "" : $this->resource->usuario_accion,
                "id_estado_actuacion" => $this->resource->id_estado_actuacion,
                "nombre_actuacion" => $this->resource->mas_actuaciones->nombre_actuacion,
                "nombre_estado_actuacion" => $this->resource->mas_estado_actuaciones->nombre,
                "etapa" => $this->resource->etapa,
                "mas_actuacion" => $this->resource->mas_actuaciones,
                "etapa_siguiente" => $this->resource->id_etapa_siguiente ? $this->resource->etapa_siguiente($this->resource->id_etapa_siguiente) : null,
                "created_user" => $this->resource->created_user,
                "estado" => $this->resource->estado,
                "id_dependencia" => $this->resource->dependencia,
                "documento_ruta" => $this->resource->documento_ruta,
                "id_dependencia2" => $this->resource->id_dependencia,
                "usuario" => $this->resource->usuario,
                "auto" => $this->resource->auto,
                "tipo_actuacion" => $this->resource->mas_actuaciones->tipo_actuacion,
                "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "updated_at" => isset($this->resource->updated_at) ? date("d/m/Y h:i:s A", strtotime($this->resource->updated_at)) : "",
                "campos_finales" => $this->resource->campos_finales != null ? json_decode($this->resource->campos_finales) : "",
                "id_estado_visibilidad" => $this->resource->id_estado_visibilidad,
                "incluir_reporte" => $this->resource->incluir_reporte,
                "actuacion_inactiva" => $this->resource->actuacion_inactiva($this->resource->getRouteKey()),
                "fecha_registro" => $this->resource->fecha_registro ? date("d/m/Y", strtotime($this->resource->fecha_registro)) : date("d/m/Y", strtotime($this->resource->created_at))
            ],
            "links" => [
                "self" => url(route("api.v1.actuaciones.show", $this->resource)),
            ],
        ];
    }
}
