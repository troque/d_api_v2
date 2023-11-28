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
            "uuid" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "se_identifica_investigado" => $this->resource->se_identifica_investigado,
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "tipo_funcionario" => $this->resource->id_tipo_funcionario,
                "documento" => $this->resource->id_tipo_documento,
                "numero_documento" => $this->resource->numero_documento,
                "nombre_completo" => $this->resource->primer_nombre.' '.$this->resource->segundo_nombre.' '.$this->resource->primer_apellido.' '.$this->resource->segundo_apellido,
                "razon_social" => $this->resource->razon_social,
                "numero_contrato" => $this->resource->numero_contrato,
                "nombre_dependencia" => $this->resource->nombreDependencia!=null?$this->resource->nombreDependencia->nombre:"",
                "observaciones" => $this->resource->observaciones,
            ],
            "links" => [
              "self" => url(route("api.v1.entidad-funcionario-qi.show", $this->resource)),
            ],
        ];
    }
}
