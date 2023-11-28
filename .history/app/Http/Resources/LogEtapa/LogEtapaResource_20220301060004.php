<?php

namespace App\Http\Resources\LogEtapa;

use Illuminate\Http\Resources\Json\JsonResource;

class LogEtapaResource extends JsonResource
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
            "type" => "log_etapa",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_proceso_disciplinario"=> $this->resource->id_proceso_disciplinario,
                "id_etapa" =>  $this->resource->id_etapa,
                "id_fase" => $this->resource->id_fase,
                "id_tipo_cambio" => $this->resource->id_tipo_cambio,
                "id_estado" => $this->resource->id_estado,
                "descripcion" => $this->resource->descripcion,
                "id_dependencia_origen"=>$this->resource->id_dependencia_origen
            ],
            "links" => [
              "self" => url(route("api.v1.log-etapa.show", $this->resource)),
            ],
        ];
    }
}
