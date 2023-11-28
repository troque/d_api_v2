<?php

namespace App\Http\Resources\TipoProceso;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoProcesoResource extends JsonResource
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
            "type" => "mas_tipo_proceso",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre" => $this->resource->nombre,
              "estado" => $this->resource->estado
            ],
            "links" => [
              "self" => url(route("api.v1.mas-tipo-proceso.show", $this->resource)),
            ],
        ];
    }
}
