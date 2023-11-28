<?php

namespace App\Http\Resources\TipoProcesoDisciplinario;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoProcesoDisciplinarioResource extends JsonResource
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
            "type" => "mas_tipo_proceso_disciplinario",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => $this->resource->nombre,
                "estado" => $this->resource->estado
            ],
            "links" => [
                "self" => url(route("api.v1.mas-tipo-proceso-disciplinario.show", $this->resource)),
            ],
        ];
    }
}
