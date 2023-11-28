<?php

namespace App\Http\Resources\Sector;

use Illuminate\Http\Resources\Json\JsonResource;

class SectorResource extends JsonResource
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
            "type" => "sector",
            "idsector" => $this->resource->idsector,
            "nombre" => $this->resource->nombre,
            "idestado" => $this->resource->idestado,
        ];
    }
}
