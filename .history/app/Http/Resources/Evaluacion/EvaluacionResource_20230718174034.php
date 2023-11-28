<?php

namespace App\Http\Resources\Evaluacion;

use App\Http\Utilidades\Utilidades;
use Illuminate\Http\Resources\Json\JsonResource;
use \YaLinqo\Enumerable as E;

class EvaluacionResource extends JsonResource
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
            "type" => "evaluacion",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "noticia_priorizada" => $this->resource->noticia_priorizada,
                "justificacion" => $this->resource->justificacion,
                "justificacion_corta" => Utilidades::getDescripcionCorta($this->resource->justificacion),
                "estado" => $this->resource->estado,
                "resultado_evaluacion" =>  $this->resource->resultado_evaluacion_entidad,
                "tipo_conducta" =>  $this->resource->tipo_conducta_entidad,
                "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "created_user" =>  $this->resource->created_user,
                "nombre_completo" => $this->resource->usuario != null ? $this->resource->usuario->nombre . ' ' . $this->resource->usuario->apellido : "",
                "fases_permitidas" =>  implode(",", E::from($this->resource->fases_permitidas)->select(function ($i) {
                    return $i->id;
                })->toArray()),
                "nombre_etapa" => $this->resource->etapa != null ? $this->resource->etapa->nombre : ' '
            ],
            "links" => [
                "self" => url(route("api.v1.evaluacion.show", $this->resource)),
            ],
        ];
    }
}
