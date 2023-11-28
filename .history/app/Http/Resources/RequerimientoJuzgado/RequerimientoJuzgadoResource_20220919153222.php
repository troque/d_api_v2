<?php

namespace App\Http\Resources\RequerimientoJuzgado;

use Illuminate\Http\Resources\Json\JsonResource;

class RequerimientoJuzgadoResource extends JsonResource
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
            "type" => "requerimiento_juzgado",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "etapa" => $this->resource->etapa,
                "id_proceso_disciplinario" => $this->resource->descripcion,
                "dependencia_origen" => $this->resource->dependenciaOrigen,
                "dependencia_destino" =>  $this->resource->dependenciaDestino,
                "id_clasificacion_radicado" => $this->resource->clasificacionRadicado,
                "enviar_otra_dependencia" => $this->resource->enviar_otra_dependencia,
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "funcionario_registra" =>$this->resource->funcionarioRegistra,
                "funcionario_asignado" =>$this->resource->funcionarioAsignado,
                "descripcion" => $this->resource->getDescripcionCorta(),
                "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
            ],
            "links" => [
              "self" => url(route("api.v1.requerimiento_juzgado.show", $this->resource)),
            ],
        ];
    }
}
