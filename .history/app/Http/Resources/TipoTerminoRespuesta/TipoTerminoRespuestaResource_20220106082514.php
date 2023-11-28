<?php

namespace App\Http\Resources\TipoTerminoRespuesta;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoTerminoRespuestaResource extends JsonResource
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
            "type" => "mas_termino_respuesta",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre" => $this->resource->nombre,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-termino-respuesta.show", $this->resource)),
            ],
        ];
    }
}
