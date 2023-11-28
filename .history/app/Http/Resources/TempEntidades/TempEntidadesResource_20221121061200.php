<?php

namespace App\Http\Resources\TempEntidades;

use Illuminate\Http\Resources\Json\JsonResource;

class TempEntidadesResource extends JsonResource
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
            "type" => "temp_entidades",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_entidad" => $this->resource->id_entidad,
                "direccion" => $this->resource->direccion,
                "sector" => $this->resource->sector,
                "nombre_investigado" => $this->resource->nombre_investigado,
                "cargo_investigado" => $this->resource->cargo_investigado,
                "radicado" => $this->resource->cargo_investigado,
                "vigencia" => $this->resource->cargo_investigado,
                "item" => $this->resource->cargo_investigado,
            ],
            "links" => [
                "self" => url(route("api.v1.temp-entidades.show", $this->resource)),
            ],
        ];
    }
}
