<?php

namespace App\Http\Resources\Semaforo;

use Illuminate\Http\Resources\Json\JsonResource;

class SemaforoResource extends JsonResource
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
            "type" => "semaforo",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "nombre" => $this->resource->nombre,
              "id_mas_evento_inicio" => $this->resource->get_id_mas_evento_inicio,
              "id_etapa" => $this->resource->id_etapa,
              "etapa" => $this->resource->etapa,
              "id_mas_actuacion_inicia" => $this->resource->get_id_mas_actuacion_inicia,
              "id_mas_dependencia_inicia" => $this->resource->get_id_mas_dependencia_inicia,
              "id_mas_grupo_trabajo_inicia" => $this->resource->get_id_mas_grupo_trabajo_inicia,
              "nombre_campo_fecha" => $this->resource->nombre_campo_fecha,
              "condiciones" => $this->resource->get_condiciones,
              "pdxsemaforo" => $this->get_procesoDisciplinario_por_semaforo,
              "estado" => $this->resource->estado,
              "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
              "created_user" => $this->resource->created_user,
              "updated_user" => $this->resource->updated_user,
              "deleted_user" => $this->resource->deleted_user,
            ],
            "links" => [
              "self" => url(route("api.v1.semaforo.show", $this->resource)),
            ],
        ];
    }
}
