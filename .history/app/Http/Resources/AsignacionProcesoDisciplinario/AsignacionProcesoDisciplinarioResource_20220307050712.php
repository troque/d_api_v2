<?php

namespace App\Http\Resources\AsignacionProcesoDisciplinario;

use Illuminate\Http\Resources\Json\JsonResource;

class AsignacionProcesoDisciplinarioResource extends JsonResource
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
            "type" => "asignacion-proceso-disciplianrio",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
              "id_dependencia" => $this->resource->id_dependencia,
              "id_etapa" => $this->resource->id_etapa,
              "created_user" => $this->resource->created_user,
            ],
            "links" => [
              "self" => url(route("api.v1.antencedentes.show", $this->resource)),
            ],
        ];
    }
}
