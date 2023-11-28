<?php

namespace App\Http\Resources\OrigenRadicado;

use Illuminate\Http\Resources\Json\JsonResource;

class OrigenRadicado2Resource extends JsonResource
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
            "type" => "mas_origen_radicado",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre" => $this->resource->nombre,
              "estado" => $this->resource->estado
            ],
            "links" => [
              "self" => url(route("api.v1.mas-origen-radicado.show", $this->resource)),
            ],
        ];
    }
}
