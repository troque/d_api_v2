<?php

namespace App\Http\Resources\MasEstadoVisibilidad;

use Illuminate\Http\Resources\Json\JsonResource;

class MasEstadoVisibilidadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            "id" => (string) $this->resource->getRouteKey(),
            "nombre" => $this->resource->nombre
        ];
    }
}
