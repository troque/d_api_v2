<?php

namespace App\Http\Resources\Caratulas;

use Illuminate\Http\Resources\Json\JsonResource;

class CaratulasResource extends JsonResource
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
            "type" => "caratulas",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => $this->resource->nombre,
                "nombre_plantilla" => $this->resource->nombre_plantilla,
                "estado" => $this->resource->estado,
                "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "created_user" => $this->resource->created_user,
            ],
            "links" => [
                "self" => url(route("api.v1.caratulas.show", $this->resource)),
            ],
        ];
    }
}