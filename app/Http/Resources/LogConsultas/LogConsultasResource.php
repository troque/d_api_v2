<?php

namespace App\Http\Resources\LogConsultas;

use Illuminate\Http\Resources\Json\JsonResource;

class LogConsultasResource extends JsonResource
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
            "type" => "log_consultas",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_usuario"=> $this->resource->usuario,
                "id_proceso_disciplinario"=> $this->resource->proceso_disciplinario,
                "filtros" => json_decode($this->resource->filtros),
                "resultados_busqueda" => $this->resource->resultados_busqueda,
                "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
            ],
            "links" => [
              "self" => url(route("api.v1.log-consultas.show", $this->resource)),
            ],
        ];
    }
}
