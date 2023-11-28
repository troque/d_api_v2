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
              "descripcion" => $this->resource->descripcion,
              "descripcion_corta" =>$this->resource->getDescripcionCorta(),
              "fecha_registro" => $this->resource->fecha_registro,
              "id_dependencia" => $this->resource->id_dependencia,
              "estado" => $this->resource->estado,
              "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
              "id_etapa" => $this->resource->id_etapa,
              "nombre_dependencia" => $this->resource->nombre_dependencia,
              "vigencia" => $this->resource->vigencia,
              "fecha_creado" => date("d/m/Y", strtotime($this->resource->created_at)),
              "nombre_etapa" => $this->resource->nombre_etapa,
              "created_user" => $this->resource->created_user,
              "etapa" =>  $this->resource->etapa,
              "dependencia" =>  $this->resource->dependencia,
            ],
            "links" => [
              "self" => url(route("api.v1.antencedentes.show", $this->resource)),
            ],
        ];
    }
}
