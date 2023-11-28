<?php

namespace App\Http\Resources\ValidarClasificacion;

use Illuminate\Http\Resources\Json\JsonResource;

class ValidarClasificacionResource extends JsonResource
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
            "type" => "validar_clasificacion",
            "id" => $this->resource->uuid,
            "attributes" => [
              "clasificacion_radicado" => $this->resource->clasificacion_radicado,
              "etapa" => $this->resource->etapa,
              "funcionario_resgitra" => $this->resource->usuario,
              "estado" => $this->resource->estado,
              "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
              "proceso_disciplinario" => $this->resource->proceso_disciplinario,
              "eliminado" => $this->resource->eliminado,
            ],
            "links" => [
              "self" => url(route("api.v1.validar-clasificacion.show", $this->resource)),
            ],
        ];
    }
}
