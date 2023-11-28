<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Resources\Json\JsonResource;

class FuncionalidadResource extends JsonResource
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
            "type" => "mas_funcionalidad",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => $this->resource->nombre,
                "nombre_mostrar" => $this->resource->nombre_mostrar,
                "id_modulo" => $this->resource->id_modulo,
                //"nombre_modulo" => $this->resource->modulo()
            ],
            "links" => [
                "self" => url(route("api.v1.funcionalidad.show", $this->resource)),
            ],
        ];
    }
}
