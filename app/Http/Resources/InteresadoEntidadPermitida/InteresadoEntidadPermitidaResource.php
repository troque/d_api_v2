<?php

namespace App\Http\Resources\InteresadoEntidadPermitida;

use Illuminate\Http\Resources\Json\JsonResource;

class InteresadoEntidadPermitidaResource extends JsonResource
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
            "type" => "mas_entidad_permitida",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre_entidad" => $this->resource->nombre_entidad,
              "id_entidad" => $this->resource->id_entidad,
              "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
              "estado" => $this->resource->estado
            ],
            "links" => [
              "self" => url(route("api.v1.mas-entidad-permitida.show", $this->resource)),
            ],
        ];
    }
}
