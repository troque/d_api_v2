<?php

namespace App\Http\Resources\TipoDocumento;

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
            "type" => "mas_tipo_documento",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre" => $this->resource->nombre,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-tipo-documento.show", $this->resource)),
            ],
        ];
    }
}
