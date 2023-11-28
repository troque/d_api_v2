<?php

namespace App\Http\Resources\EvaluacionTipoExpediente;

use Illuminate\Http\Resources\Json\JsonResource;

class EvaluacionFaseResource extends JsonResource
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
            "type" => "evaluacion_fases_activas",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "id_fase_actual" => $this->resource->id_fase_actual,
              "id_fase_antecesora" => $this->resource->id_fase_antecesora,
              "id_resultado_evaluacion" => $this->resource->id_resultado_evaluacion,
              "id_tipo_expediente" => $this->resource->id_tipo_expediente,
              "id_sub_tipo_expediente" => $this->resource->id_sub_tipo_expediente,
              "orden" => $this->resource->orden
            ],
            "links" => [
              "self" => url(route("api.v1.evaluacion-tipo-expediente.show", $this->resource)),
            ],
        ];
    }
}
