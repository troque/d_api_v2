<?php

namespace App\Http\Resources\Buscador;

use Illuminate\Http\Resources\Json\JsonResource;

class BuscadorResource extends JsonResource
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
            "type" => "buscador",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "log_proceso_disciplinario" => $this->resource->log_proceso_disciplinario,
                "etapa" => $this->resource->etapa->nombre,
                "dependenciaOrigen" => $this->resource->dependenciaOrigen->nombre,
            ],
            "links" => [
                "self" => url(route("api.v1.buscador.show", $this->resource)),
            ],
        ];
    }
}