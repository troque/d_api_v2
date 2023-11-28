<?php

namespace App\Http\Resources\Ciudad;

use Illuminate\Http\Resources\Json\JsonResource;

class CiudadResource extends JsonResource
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




            "type" => "ciudades",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
                "estado" => $this->resource->estado,
                "codigo_dane" => $this->resource->codigo_dane,
                "id_departamento" => $this->resource->id_departamento,
                "departamento" => $this->resource->departamento,
            ],
            "links" => [
                "self" => url(route("api.v1.ciudades.show", $this->resource)),
            ],
        ];
    }
}
