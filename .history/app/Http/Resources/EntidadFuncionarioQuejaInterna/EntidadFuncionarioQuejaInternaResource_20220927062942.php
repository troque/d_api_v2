<?php

namespace App\Http\Resources\EntidadFuncionarioQuejaInterna;

use Illuminate\Http\Resources\Json\JsonResource;

class EntidadFuncionarioQuejaInternaResource extends JsonResource
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
            "type" => "entidad_funcionario_queja_interna",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "se_identifica_investigado" => $this->resource->se_identifica_investigado,
                "id_entidad_investigado" => $this->resource->id_entidad_investigado,
                "id_tipo_funcionario" => $this->resource->id_tipo_funcionario,
                "id_tipo_documento" => $this->resource->id_tipo_documento,
                "numero_documento" => $this->resource->numero_documento,
                "primer_nombre" => $this->resource->primer_nombre,
                "segundo_nombre" => $this->resource->segundo_nombre,
                "primer_apellido" => $this->resource->primer_apellido,
                "segundo_apellido" => $this->resource->segundo_apellido,
                "razon_social" => $this->resource->razon_social,
                "numero_contrato" => $this->resource->numero_contrato,
                "dependencia" => $this->resource->dependencia,
                "observaciones" => $this->resource->dependencia,
            ],
            "links" => [
              "self" => url(route("api.v1.entidad-funcionario-queja-interna.show", $this->resource)),
            ],
        ];
    }
}
