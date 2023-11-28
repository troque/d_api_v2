<?php

namespace App\Http\Resources\ProcesoSinproc;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcesoSinprocResource extends JsonResource
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
            "type" => "proceso_sinproc",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "radicado" => null,
              "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
            ],
            "links" => [
              "self" => url(route("api.v1.proceso-diciplinario.show", $this->resource)),
            ],
        ];
    }
}
