<?php

namespace App\Http\Resources\ClasificacionRadicado;

use Illuminate\Http\Resources\Json\JsonResource;
use \YaLinqo\Enumerable as E;

class ClasificacionRadicadoResource extends JsonResource
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
            "type" => "clasificacion_radicado",
            //"id" => "(string) $this->resource->getRouteKey()",
            "id" => $this->resource->uuid,
            "attributes" => [
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "id_etapa" => $this->resource->id_etapa,
                "id_tipo_expediente" => $this->resource->id_tipo_expediente,
                "observaciones" => $this->resource->observaciones,
                "observacion_corta" => $this->resource->getObservacionCorta(),
                "id_tipo_queja" => $this->resource->id_tipo_queja,
                "id_termino_respuesta" => $this->resource->id_termino_respuesta,
                "fecha_termino" => date("d/m/Y", strtotime($this->resource->fecha_termino)),
                "hora_termino" => $this->resource->hora_termino,
                "gestion_juridica" => $this->resource->gestion_juridica,
                "estado" => $this->resource->estado,
                "nombre_estado" => $this->resource->estado == 1 ? "ACTIVO" : "INACTIVO",
                "id_estado_reparto" => $this->resource->id_estado_reparto,
                "expediente" =>  $this->resource->expediente,
                "tipo_queja" =>  $this->resource->tipo_queja,
                "created_at" =>  date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "id_tipo_derecho_peticion" =>  $this->resource->id_tipo_derecho_peticion,
                "oficina_control_interno" =>  $this->resource->oficina_control_interno,
                "tipo_derecho_peticion" =>  $this->resource->tipo_derecho_peticion,
                "created_user" => $this->resource->created_user,
                "nombre_completo" => $this->resource->usuario != null ? $this->resource->usuario->nombre . ' ' . $this->resource->usuario->apellido : "",
                "reclasificacion" => $this->resource->reclasificacion,
                "proceso_disciplinario" => $this->resource->procesoDisciplinario,
                "etapa" => $this->resource->etapa,
                "fases_permitidas" =>  implode(",", E::from($this->resource->fases_permitidas)->select(function ($i) {
                    return $i->id;
                })->toArray()),
                "usuario_registra" => $this->resource->usuarioRegistra,
                "dependencia" => $this->resource->dependencia,
                "log" => $this->resource->log,
                "validacion_jefe" => $this->resource->validacion_jefe,
                "mensaje_de_terminos" => $this->resource->mensaje_de_terminos(),

            ],
            "links" => [
                "self" => url(route("api.v1.clasificacion-radicado.show", $this->resource)),
            ],
        ];
    }
}
