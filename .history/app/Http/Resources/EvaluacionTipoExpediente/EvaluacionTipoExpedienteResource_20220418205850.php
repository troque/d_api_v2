<?php

namespace App\Http\Resources\EvaluacionTipoExpediente;

use Illuminate\Http\Resources\Json\JsonResource;

class EvaluacionTipoExpedienteResource extends JsonResource
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
            "type" => "evaluacion_tipo_expediente",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "id_proceso_disciplinario" => $this->resource->nombre,
              "aceptado" => $this->resource->estado,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-tipo-queja.show", $this->resource)),
            ],
        ];
    }
}
