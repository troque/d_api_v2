<?php

namespace App\Http\Resources\ComunicacionInteresado;

use Illuminate\Http\Resources\Json\JsonResource;

class ComunicacionInteresadoResource extends JsonResource
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
            "type" => "comunicacion_interesado",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_interesado" => $this->resource->id_interesado,
                "documento_sirius" => $this->resource->id_documento_sirius,
                "documento" => $this->resource->documento,
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "primer_nombre" => $this->resource->primer_nombre,
                "radicado" => $this->resource->radicado,
                "archivo" => $this->resource->archivo,
                "estado" => $this->resource->estado
            ],
            "links" => [
                "self" => url(route("api.v1.comunicacion-interesado.show", $this->resource)),
            ],
        ];
    }
}
