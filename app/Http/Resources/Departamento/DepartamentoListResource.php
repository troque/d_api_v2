<?php

namespace App\Http\Resources\Departamento;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartamentoListResource extends JsonResource
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
              "nombre" => $this->resource->nombre,
              "codigo_dane" => $this->resource->codigo_dane
            ],
            "links" => [
              "self" => url(route("api.v1.departments.show", $this->resource)),
            ],
        ];
    }
}