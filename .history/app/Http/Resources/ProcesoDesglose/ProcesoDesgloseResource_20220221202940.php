<?php

namespace App\Http\Resources\ProcesoDesglose;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcesoDesgloseResource extends JsonResource
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
            "type" => "proceso_desglose",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "radicado" => null,
              "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
              "proceso_desglose" => $this->resource->procesoDesglose,
            ],
            "links" => [
              "self" => url(route("api.v1.proceso-diciplinario.show", $this->resource)),
            ],
        ];
    }
}
