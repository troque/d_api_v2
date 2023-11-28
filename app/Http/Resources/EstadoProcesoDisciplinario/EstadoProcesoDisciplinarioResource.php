<?php

namespace App\Http\Resources\EstadoProcesoDisciplinario;

use Illuminate\Http\Resources\Json\JsonResource;

class EstadoProcesoDisciplinarioResource extends JsonResource
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
            "type" => "mas_estado_proceso_disciplinario",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "id" => $this->resource->getRouteKey(),
              "nombre" => $this->resource->nombre,
              "estado" => $this->resource->estado,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-estado-proceso-disciplinario.show", $this->resource)),
            ],
        ];
    }
}
