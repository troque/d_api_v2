<?php

namespace App\Http\Resources\Vigencia;

use Illuminate\Http\Resources\Json\JsonResource;

class VigenciaResource extends JsonResource
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
            "type" => "mas_vigencia",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "vigencia" => $this->resource->vigencia,
              "estado" => $this->resource->estado,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-vigencia.show", $this->resource)),
            ],
        ];
    }
}
