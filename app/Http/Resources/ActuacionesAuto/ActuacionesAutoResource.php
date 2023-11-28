<?php

namespace App\Http\Resources\ActuacionesAuto;

use Illuminate\Http\Resources\Json\JsonResource;

class ActuacionesAutoResource extends JsonResource
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
            "type" => "actuaciones",
            "attributes" => [
                "id_actuacion" => $this->resource->uuid,
                "nombre_actuacion" => $this->resource->nombre_actuacion,
                "auto" => $this->resource->auto,
                "fecha_actualizacion" => date("d/m/Y", strtotime($this->resource->fecha_actualizacion)),
            ],
        ];
    }
}
