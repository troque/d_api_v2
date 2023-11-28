<?php

namespace App\Http\Resources\TipoExpediente;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoExpedienteResource extends JsonResource
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
            "type" => "mas_tipo_expediente",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre" => $this->resource->nombre,
              "estado" => $this->resource->estado,
              "termino" => $this->resource->termino
            ],
            "links" => [
              "self" => url(route("api.v1.mas-tipo-expediente.show", $this->resource)),
            ],
        ];
    }
}
