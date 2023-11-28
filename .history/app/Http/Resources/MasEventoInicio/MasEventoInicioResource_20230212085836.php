<?php

namespace App\Http\Resources\MasEventoInicio;

use Illuminate\Http\Resources\Json\JsonResource;

class MasEventoInicioResource extends JsonResource
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
            "type" => "mas_evento_inicio",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
                "estado" => $this->resource->estado,
                "created_user" => $this->resource->created_user,
                "updated_user" => $this->resource->updated_user,
                "deleted_user" => $this->resource->deleted_user,
            ],
            "links" => [
                "self" => url(route("api.v1.mas_evento_inicio.show", $this->resource)),
            ],
        ];
    }
}
