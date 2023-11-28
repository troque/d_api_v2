<?php

namespace App\Http\Resources\EntidadInvestigado;

use Illuminate\Http\Resources\Json\JsonResource;

class EntidadInvestigadoResource extends JsonResource
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
            "type" => "entidad_investigado",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "id_etapa" => $this->resource->id_etapa,
                "nombre_etapa" => $this->resource->etapa->nombre,
                "id_entidad" => $this->resource->id_entidad,
                "nombre_entidad" => $this->resource->nombre_entidad ? $this->resource->nombre_entidad : "NO APLICA",
                "nombre_investigado" => $this->resource->nombre_investigado != null ? $this->resource->nombre_investigado : "NO APLICA",
                "cargo" => $this->resource->cargo != null ? $this->resource->cargo : "NO APLICA",
                "codigo" => $this->resource->codigo,
                "observaciones" => $this->resource->observaciones,
                "observacion_corta" => $this->resource->getObservacionCorta(),
                "estado" => $this->resource->estado,
                "nombre_estado" => $this->resource->estado == 1 ? "ACTIVO" : "INACTIVO",
                "requiere_registro" => $this->resource->requiere_registro,
                "created_at" =>  date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "created_user" =>  $this->resource->created_user,
                "nombre_completo" => $this->resource->usuario != null ? $this->resource->usuario->nombre . ' ' . $this->resource->usuario->apellido : "",
                "investigado" => $this->resource->investigado,
                "contratista" => $this->resource->contratista,
                "planta" => $this->resource->planta,
                "comentario_identifica_investigado" => $this->resource->comentario_identifica_investigado,
                "nombre_sector" => $this->resource->nombre_sector != null ? $this->resource->nombre_sector : "NO APLICA",
                "usuario" => $this->resource->getDependenciaUsuario($this->resource->usuario->id_dependencia),
            ],
            "links" => [
                "self" => url(route("api.v1.entidad-investigado.show", $this->resource)),
            ],
        ];
    }
}
