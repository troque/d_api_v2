<?php

namespace App\Http\Resources\ParametroCamposCaratula;

use Illuminate\Http\Resources\Json\JsonResource;

class ParametroCamposCaratulaResource extends JsonResource
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
            "type" => "parametro_campos_caratula",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre_campo" => mb_strtoupper($this->resource->nombre_campo),
                "type" => $this->resource->type,
                "value" => $this->resource->value,
                "estado" => $this->resource->estado,
                "fecha_registro" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at))
            ],
            "links" => [
                "self" => url(route("api.v1.parametro_campos_caratula.show", $this->resource)),
            ],
        ];
    }
}
