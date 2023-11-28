<?php

namespace App\Http\Resources\TipoEstadoEtapa;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoEstadoEtapaResource extends JsonResource
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
            "type" => "mas_tipo_estado_etapa",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre" => $this->resource->nombre,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-estado-etapa.show", $this->resource)),
            ],
        ];
    }
}
