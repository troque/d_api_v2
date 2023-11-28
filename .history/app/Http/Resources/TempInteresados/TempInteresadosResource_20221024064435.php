<?php

namespace App\Http\Resources\TempInteresados;

use Illuminate\Http\Resources\Json\JsonResource;

class TempInteresadosResource extends JsonResource
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
            "type" => "temp_interesados",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "uuid" => $this->resource->uuid,
                "proceso_disciplinario" => $this->resource->id_temp_proceso_disciplinario,
                "id_etapa" => $this->resource->id_etapa,
                "nombre_investigado" => $this->resource->nombre_investigado,
                "id_entidad" => $this->resource->id_entidad,
            ],
            "links" => [
                "self" => url(route("api.v1.temp-interesados.show", $this->resource)),
            ],
        ];
    }
}
