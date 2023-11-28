<?php

namespace App\Http\Resources\ProcesoPoderPreferente;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcesoPoderPreferenteResource extends JsonResource
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
            "type" => "proceso_poder_preferente",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "radicado" => null
            ],
            "links" => [
              "self" => url(route("api.v1.proceso-diciplinario.show", $this->resource)),
            ],
        ];
    }
}
