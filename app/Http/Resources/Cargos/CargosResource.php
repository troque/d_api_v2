<?php

namespace App\Http\Resources\Cargos;

use Illuminate\Http\Resources\Json\JsonResource;

class CargosResource extends JsonResource
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
            "type" => "cargos",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
                "estado" => $this->resource->estado,
                "created_at" =>  date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
            ],
            "links" => [
                "self" => url(route("api.v1.cargos.show", $this->resource)),
            ],
        ];
    }
}
