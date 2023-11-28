<?php

namespace App\Http\Resources\DireccionBis;

use Illuminate\Http\Resources\Json\JsonResource;

class DireccionBisResource extends JsonResource
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
            "type" => "mas_direccion_bis",
            // "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre" => $this->resource->nombre,
              "json" => $this->resource->json,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-direccion-bis.show", $this->resource)),
            ],
        ];
    }
}
