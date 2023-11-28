<?php

namespace App\Http\Resources\Antecedente;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Retorna la información de un antecedente
 *
 * @autor: Diego Saavedra
 * @Fecha: 22 diciembre 2021
 */
class AntecedenteResource extends JsonResource
{
    /**
     * Transforma the resource en un array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "type" => "antecedente",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "descripcion" => $this->resource->descripcion,
                "descripcion_corta" => $this->resource->getDescripcionCorta(),
                "fecha_registro" => date("d/m/Y", strtotime($this->resource->fecha_registro)),
                "id_dependencia" => $this->resource->id_dependencia,
                "estado" => $this->resource->estado,
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "id_etapa" => $this->resource->id_etapa,
                "nombre_dependencia" => $this->resource->dependencia == null ? "" : $this->resource->dependencia->nombre,
                "vigencia" => $this->resource->vigencia,
                //"fecha_creado" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "fecha_creado" => date("d/m/Y ", strtotime($this->resource->fecha_registro)),
                "nombre_etapa" => $this->resource->nombre_etapa,
                "created_user" => $this->resource->created_user,
                "nombre_completo" => $this->resource->usuario != null ? $this->resource->usuario->nombre . ' ' . $this->resource->usuario->apellido : "",
                "etapa" =>  $this->resource->etapa,
                "dependencia" =>  $this->resource->dependencia
            ],
            "links" => [
                "self" => url(route("api.v1.antencedentes.show", $this->resource)),
            ],
        ];
    }
}
