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
                "tipo_interesado" => $this->resource->id_temp_proceso_disciplinario,
                "tipo_sujeto_procesal" => $this->resource->id_etapa,
                "primer_nombre" => $this->resource->id_tipo_interesado,
                "segundo_nombre" => $this->resource->estado,
                "primer_apellido" => $this->resource->id_funcionario,
                "segundo_apellido" => $this->resource->tipo_documento,
                "tipo_documento" => $this->resource->numero_documento,
                "numero_documento" => $this->resource->primer_nombre,
                "email" => $this->resource->segundo_nombre,
                "telefono" => $this->resource->direccion,
                "cargo" => $this->resource->id_dependencia,
                "orientacion_sexual" => $this->resource->id_dependencia,
                "sexo" => $this->resource->id_dependencia,
                "direccion" => $this->resource->id_dependencia,
                "departamento" => $this->resource->id_dependencia,
                "ciudad" => $this->resource->id_dependencia,
                "localidad" => $this->resource->id_dependencia,
                "entidad" => $this->resource->id_dependencia,
                "sector" => $this->resource->id_dependencia,
                "radicado" => $this->resource->id_dependencia,
                "vigencia" => $this->resource->id_dependencia,
                "item" => $this->resource->id_dependencia,
            ],
            "links" => [
                "self" => url(route("api.v1.temp-interesados.show", $this->resource)),
            ],
        ];
    }
}
