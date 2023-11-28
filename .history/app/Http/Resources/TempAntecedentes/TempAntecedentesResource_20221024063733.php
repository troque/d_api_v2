<?php

namespace App\Http\Resources\TempAntecedentes;

use Illuminate\Http\Resources\Json\JsonResource;

class TempAntecedentesResource extends JsonResource
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
            "type" => "temp_antecedentes",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "uuid" => $this->resource->uuid,
                "proceso_disciplinario" => $this->resource->id_temp_proceso_disciplinario,
                "descripcion" => $this->resource->descripcion,
                "fecha_registro" => $this->resource->fecha_registro,
                "estado" => $this->resource->estado,
                "etapa" => $this->resource->id_etapa,
            ],
            "links" => [
                "self" => url(route("api.v1.temp_actuaciones.show", $this->resource)),
            ],
        ];
    }
}
