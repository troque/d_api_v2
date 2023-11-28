<?php

namespace App\Http\Resources\PortalLog;

use Illuminate\Http\Resources\Json\JsonResource;

class PortalLogResource extends JsonResource
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
            "type" => "portal-log",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "portal_id_user" => $this->resource->portal_id_user,
                "informacion_interesado" => $this->resource->mas_interesados($this->resource->portal_id_user),
                "detalle" => $this->resource->detalle,
                "informacion_equipo" => $this->resource->informacion_equipo,
                "estado" => $this->resource->estado,
                "fecha_registro" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at))
            ],
            "links" => [
                "self" => url(route("api.v1.portal-log.show", $this->resource)),
            ],
        ];
    }
}