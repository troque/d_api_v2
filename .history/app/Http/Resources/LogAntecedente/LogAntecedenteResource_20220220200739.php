<?php

namespace App\Http\Resources\LogAntecedente;

use Illuminate\Http\Resources\Json\JsonResource;

class LogAntecedenteResource extends JsonResource
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
            "type" => "log_antecedente",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_antecedente" =>  $this->resource->id_antecedente,
                "observacion_estado" =>  $this->resource->observacion_estado,
                "descripcion" => $this->resource->descripcion,
            ],
            "links" => [
              "self" => url(route("api.v1.log-antecedentes.show", $this->resource)),
            ],
        ];
    }
}
