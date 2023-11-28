<?php

namespace App\Http\Resources\EstadoActuaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class EstadoActuacionesResource extends JsonResource
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
            "type" => "estado-actuaciones",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => $this->resource->nombre,
                "codigo" => $this->resource->codigo,
                "descripcion" => $this->resource->descripcion,
                "created_user" => $this->resource->created_user,
                "updated_user" => $this->resource->updated_user,
            ],
            "links" => [
                "self" => url(route("api.v1.estado-actuaciones.show", $this->resource)),
            ],
        ];
    }
}