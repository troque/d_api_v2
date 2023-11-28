<?php

namespace App\Http\Resources\RemisionQueja;

use App\Models\DependenciaOrigenModel;
use Illuminate\Http\Resources\Json\JsonResource;

class RemisionQuejaResource extends JsonResource
{

    protected $usuario;

    public function setUsuario($usuario){
        $this->usuario = $usuario;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "type" => "remision_queja",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "fecha_creacion" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "dependencia_origen" => $this->resource->dependencia_origen,
                "dependencia_destino" => $this->resource->dependencia_destino,
                "usuario_jefe_origen" => $this->resource->getUsuarioJefeDependencia($this->resource->dependencia_origen->id_usuario_jefe),
                "usuario_jefe_destino" => $this->resource->getUsuarioJefeDependencia($this->resource->dependencia_destino->id_usuario_jefe),
                "evaluacion" => $this->resource->getTipoEvaluacion($this->resource->id_tipo_evaluacion),
                "Idevaluacion" => $this->resource->id_tipo_evaluacion,
                "incorporacion" => $this->resource->getIncorporacion
            ],
            "links" => [
              "self" => url(route("api.v1.remision-queja.show", $this->resource)),
            ],
        ];
    }
}
