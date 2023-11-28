<?php

namespace App\Http\Resources\TipoConductaProcesoDisciplinario;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoConductaProcesoDisciplinarioResource extends JsonResource
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
            "type" => "mas_tipo_conducta",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "conducta_nombre" => $this->resource->conducta_nombre,
              "estado" => $this->resource->estado,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-tipo-conducta.show", $this->resource)),
            ],
        ];
    }
}
