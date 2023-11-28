<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Resources\Json\JsonResource;
use \YaLinqo\Enumerable as E;

class ModuloGrupoResource extends JsonResource
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
            "type" => "usuario",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => $this->resource->nombre,
                "nombre_mostrar" => mb_strtoupper($this->resource->nombre_mostrar),
                "funcionalidades" => $this->resource->funcionalidades,
                //"funcionalidades" => implode(",", E::from($this->resource->funcionalidades)->select(function($i){ return $i->nombre; })->toArray()),
                //"ids_funcionalidades" => implode(",", E::from($this->resource->funcionalidades)->select(function($i){ return $i->id; })->toArray()),
            ],
            "links" => [
                "self" => url(route("api.v1.usuario.show", $this->resource)),
            ],
        ];
    }
}
