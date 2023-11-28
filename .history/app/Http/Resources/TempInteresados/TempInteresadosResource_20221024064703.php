<?php

namespace App\Http\Resources\TempInteresados;

use Illuminate\Http\Resources\Json\JsonResource;

class TempInteresadosResource extends JsonResource
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
            "type" => "temp_interesados",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "uuid" => $this->resource->uuid,
                "proceso_disciplinario" => $this->resource->id_temp_proceso_disciplinario,
                "id_etapa" => $this->resource->id_etapa,
                "id_tipo_interesado" => $this->resource->id_tipo_interesado,
                "estado" => $this->resource->estado,
                "id_funcionario" => $this->resource->id_funcionario,
                "tipo_documento" => $this->resource->tipo_documento,
                "numero_documento" => $this->resource->numero_documento,
                "primer_nombre" => $this->resource->primer_nombre,
                "segundo_nombre" => $this->resource->segundo_nombre,
                "direccion" => $this->resource->direccion,
                "id_dependencia" => $this->resource->id_dependencia,
            ],
            "links" => [
                "self" => url(route("api.v1.temp-interesados.show", $this->resource)),
            ],
        ];
    }
}
