<?php

namespace App\Http\Resources\TrazabilidadActuaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class TrazabilidadActuacionesResource extends JsonResource
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
            "type" => "trazabilidad-actuaciones",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id" => $this->resource->id,
                "uuid_actuacion" => $this->resource->uuid_actuacion,
                "id_estado_actuacion" => $this->resource->id_estado_actuacion,
                "nombre_estado_actuacion" => $this->resource->mas_estado_actuacion->nombre,
                "observacion" => $this->resource->observacion,
                "estado" => $this->resource->estado,
                "id_dependencia" => empty($this->resource->dependencia) ? "" : $this->resource->dependencia,
                "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "created_user" => $this->resource->created_user,
                "updated_user" => empty($this->resource->updated_user) ? "-" : $this->resource->updated_user,
                "usuario" => $this->resource->usuario,
                "actuaciones_inactivas" => $this->resource->actuacionesInactivas($this->resource->id)

            ],
            // "links" => [
            //     "self" => url(route("api.v1.trazabilidad-actuaciones.show", $this->resource)),
            // ],
        ];
    }
}
