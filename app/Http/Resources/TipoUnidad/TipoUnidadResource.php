<?php

namespace App\Http\Resources\TipoUnidad;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoUnidadResource extends JsonResource
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
            "type" => "mas_tipo_unidad",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => $this->resource->nombre,
                "codigo_unidad" => $this->resource->codigo_unidad,
                "descripcion_unidad" => $this->resource->descripcion_unidad,
                "dependencia" => $this->resource->mas_dependencia_origen,
                "estado" => $this->resource->estado,
            ],
            "links" => [
                "self" => url(route("api.v1.mas_tipo_unidad.show", $this->resource)),
            ],
        ];
    }
}