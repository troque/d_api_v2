<?php

namespace App\Http\Resources\Departamento;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartamentoResource extends JsonResource
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
            "type" => "departamentos",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
                "codigo_dane" => $this->resource->codigo_dane,
                "estado" => $this->resource->estado,
                //"ciudades" => $this->resource->ciudades,
            ],
            "links" => [
                "self" => url(route("api.v1.departments.show", $this->resource)),
            ],
        ];
    }
}
