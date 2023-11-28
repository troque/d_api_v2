<?php

namespace App\Http\Resources\ParametrizacionActuaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class ParametrizacionActuacionesResource extends JsonResource
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
            "type" => "parametrizacion-actuaciones",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre_actuacion" => $this->resource->nombre_actuacion,
                "nombre_plantilla" => $this->resource->nombre_plantilla,
                "id_etapa" => $this->resource->id_etapa,
                "estado" => $this->resource->estado,
                "id_etapa_despues_aprobacion" => $this->resource->id_etapa_despues_aprobacion,
                "despues_aprobacion_listar_actuacion" => $this->resource->despues_aprobacion_listar_actuacion,
            ],
            "links" => [
                "self" => url(route("api.v1.parametrizacion-actuaciones.show", $this->resource)),
            ],
        ];
    }
}