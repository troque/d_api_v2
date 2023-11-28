<?php

namespace App\Http\Resources\ResultadoEvaluacion;

use Illuminate\Http\Resources\Json\JsonResource;

class ResultadoEvaluacionResource extends JsonResource
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
            "type" => "mas_resultado_evaluacion",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
                "estado" => $this->resource->estado,
            ],
            "links" => [
                "self" => url(route("api.v1.mas-resultado-evaluacion.show", $this->resource)),
            ],
        ];
    }
}
