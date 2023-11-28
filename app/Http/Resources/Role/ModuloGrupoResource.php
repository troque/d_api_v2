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
            "type" => "modulo_grupo",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => $this->resource->nombre,
                "orden" => $this->resource->orden,
                "estado" => $this->resource->estado,
            ],
            "links" => [
                "self" => url(route("api.v1.modulo-grupo.show", $this->resource)),
            ],
        ];
    }
}
