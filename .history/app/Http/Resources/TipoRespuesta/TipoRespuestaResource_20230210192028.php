<?php

namespace App\Http\Resources\TipoRespuesta;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoRespuestaResource extends JsonResource
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
            "type" => "mas_tipo_respuesta",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
            ],
            "links" => [
                "self" => url(route("api.v1.mas-tipo-respuesta.show", $this->resource)),
            ],
        ];
    }
}
