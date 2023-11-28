<?php

namespace App\Http\Resources\DireccionComplemento;

use Illuminate\Http\Resources\Json\JsonResource;

class DireccionComplementoResource extends JsonResource
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
            "type" => "mas_direccion_complemento",
            // "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre" => $this->resource->nombre,
              "json" => $this->resource->json,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-direccion-complemento.show", $this->resource)),
            ],
        ];
    }
}
