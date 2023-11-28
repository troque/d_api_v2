<?php

namespace App\Http\Resources\Fase;

use Illuminate\Http\Resources\Json\JsonResource;

class FaseNombreResource extends JsonResource
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
            "type" => "mas_fase",
            "attributes" => [
                "nombre" => $this->resource->nombre,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-fase.show", $this->resource)),
            ],
        ];
    }
}
