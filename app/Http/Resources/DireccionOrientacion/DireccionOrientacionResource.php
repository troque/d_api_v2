<?php

namespace App\Http\Resources\DireccionOrientacion;

use Illuminate\Http\Resources\Json\JsonResource;

class DireccionOrientacionResource extends JsonResource
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
            "type" => "mas_direccion_orientacion",
            // "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre" => $this->resource->nombre,
              "json" => $this->resource->json,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-direccion-orientacion.show", $this->resource)),
            ],
        ];
    }
}
