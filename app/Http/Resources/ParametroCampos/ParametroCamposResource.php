<?php

namespace App\Http\Resources\ParametroCampos;

use Illuminate\Http\Resources\Json\JsonResource;

class ParametroCamposResource extends JsonResource
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
            "type" => "parametro_campos",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre_campo" => $this->resource->nombre_campo,
                "type" => $this->resource->type,
                "value" => $this->resource->value,
                "estado" => $this->resource->estado
            ],
            "links" => [
                "self" => url(route("api.v1.parametro-campos.show", $this->resource)),
            ],
        ];
    }
}