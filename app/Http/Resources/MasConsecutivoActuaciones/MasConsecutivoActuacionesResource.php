<?php

namespace App\Http\Resources\MasConsecutivoActuaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class MasConsecutivoActuacionesResource extends JsonResource
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
            "type" => "mas-consecutivo-actuaciones",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_vigencia" => $this->resource->vigencia,
                "consecutivo" => $this->resource->consecutivo,
                "id_actuacion" => $this->resource->id_actuacion,
                "estado" => $this->resource->estado,
                "created_at" => $this->resource->created_at,
                "updated_at" => $this->resource->updated_at,
            ],
            "links" => [
                "self" => url(route("api.v1.mas-consecutivo-actuaciones.show", $this->resource)),
            ],
        ];
    }
}
