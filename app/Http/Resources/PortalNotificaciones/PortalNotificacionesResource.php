<?php

namespace App\Http\Resources\PortalNotificaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class PortalNotificacionesResource extends JsonResource
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
            "type" => "portal-notificaciones",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "uuid_proceso_disciplinario" => $this->resource->proceso_disciplinario,
                "numero_documento" => $this->resource->numero_documento,
                "tipo_documento" => $this->resource->tipo_documento,
                "detalle" => $this->resource->detalle,
                "detalle_incompleto" => $this->resource->detalle ? substr($this->resource->detalle, 0, 150) : "",
                "radicado" => $this->resource->radicado,
                "detalle" => $this->resource->detalle,
                "estado" => $this->resource->estado,
                "usuario_envia" => $this->resource->usuario->nombre . " " . $this->resource->usuario->apellido,
                "fecha_registro" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "logs" => $this->resource->logs($this->resource->getRouteKey()),
                "actuacion" => $this->resource->actuacion($this->resource->id_actuacion),
            ],
            "links" => [
                "self" => url(route("api.v1.portal-notificaciones.show", $this->resource)),
            ],
        ];
    }
}