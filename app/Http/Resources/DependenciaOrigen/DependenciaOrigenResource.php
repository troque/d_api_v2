<?php

namespace App\Http\Resources\DependenciaOrigen;

use Illuminate\Http\Resources\Json\JsonResource;
use \YaLinqo\Enumerable as E;

class DependenciaOrigenResource extends JsonResource
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
            "type" => "mas_dependencia_origen",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
                "estado" => $this->resource->estado,
                "id_usuario_jefe" => $this->resource->id_usuario_jefe,
                "usuario_jefe" => $this->resource->usuario_jefe,
                "nombre_solo_usuario_jefe" => $this->resource->usuario_jefe == null ? "" : $this->resource->usuario_jefe->name,
                "nombre_usuario_jefe" => mb_strtoupper($this->resource->usuario_jefe == null ? "" : $this->resource->usuario_jefe->nombre . ' ' . $this->resource->usuario_jefe->apellido),
                "email_usuario_jefe" => $this->resource->usuario_jefe == null ? "" : $this->resource->usuario_jefe->email,
                //"ids_accesos" => implode(",", E::from($this->resource->accesos)->select(function($i){ return $i->id; })->toArray()),
                "accesos" => $this->resource->porcentajeAsignacion(implode(",", E::from($this->resource->accesos)->select(function ($i) {
                    return $i->id;
                })->toArray()), (string) $this->resource->getRouteKey()),
                "dependenciaActuacion" => $this->resource->dependenciaActuacion,
                "prefijo" => $this->resource->prefijo,
            ],
            "links" => [
                "self" => url(route("api.v1.mas-dependencia-origen.show", $this->resource)),
            ],
        ];
    }
}
