<?php

namespace App\Http\Resources\TipoSujetoProcesal;

use Illuminate\Http\Resources\Json\JsonResource;

class TempActuacionesResource extends JsonResource
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
            "type" => "temp_actuaciones",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "uuid" => $this->resource->uuid,
                "proceso_disciplinario" => $this->resource->id_temp_proceso_disciplinario,
                "proceso_disciplinario" => $this->resource->auto,
            ],
            "links" => [
                "self" => url(route("api.v1.temp_actuaciones.show", $this->resource)),
            ],
        ];
    }
}
