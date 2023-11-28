<?php

namespace App\Http\Resources\MasActuacionesEtapa;

use Illuminate\Http\Resources\Json\JsonResource;

class ActuacionesEtapaResource extends JsonResource
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
            "type" => "mas_actuaciones",
            "id" => (string) $this->resource->id,
            "attributes" => [
              "nombre_actuacion" => $this->resource->nombre_actuacion
            ]
          ];
    }
}
