<?php

namespace App\Http\Resources\TipoFirma;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoFirmaResource extends JsonResource
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
            "type" => "mas_tipo_firma",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
                "estado" => $this->resource->estado,
                "tamano" => $this->resource->tamano,
            ],
            "links" => [
                "self" => url(route("api.v1.mas_tipo_firma.show", $this->resource)),
            ],
        ];
    }
}
