<?php

namespace App\Http\Resources\TempEntidades;

use Illuminate\Http\Resources\Json\JsonResource;

class TempEntidadesResource extends JsonResource
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
            "type" => "temp_clasificacion_radicado",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "uuid" => $this->resource->uuid,
                "proceso_disciplinario" => $this->resource->id_temp_proceso_disciplinario,
                "id_etapa" => $this->resource->id_etapa,
                "id_tipo_expediente" => $this->resource->id_tipo_expediente,
            ],
            "links" => [
                "self" => url(route("api.v1.temp-clasificacion-radicado.show", $this->resource)),
            ],
        ];
    }
}
