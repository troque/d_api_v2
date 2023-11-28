<?php

namespace App\Http\Resources\CierreEtapa;

use Illuminate\Http\Resources\Json\JsonResource;

class CierreEtapaResource extends JsonResource
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
            "type" => "cierre_etapa",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "id_etapa" => $this->resource->id_etapa,
                "created_at" =>  date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "etapa" => $this->resource->etapa,
                "created_user" => $this->resource->created_user,
                "funcionario_asignado" => $this->resource->funcionarioAsignado,
                "proceso_disciplinario" => $this->resource->proceso_disciplinario,
            ],
            "links" => [
                "self" => url(route("api.v1.cierre-etapa.show", $this->resource)),
            ],
        ];
    }
}
