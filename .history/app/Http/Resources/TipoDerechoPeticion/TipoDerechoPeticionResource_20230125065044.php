<?php

namespace App\Http\Resources\TipoDerechoPeticion;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoDerechoPeticionResource extends JsonResource
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
            "type" => "mas_tipo_derecho_peticion",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre" => $this->resource->nombre,
              "observacion" => $this->resource->observacion,
              "estado" => $this->resource->estado
            ],
            // "links" => [
            //   "self" => url(route("api.v1.mas-tipo-derecho-peticion.show", $this->resource)),
            // ],
        ];
    }
}
