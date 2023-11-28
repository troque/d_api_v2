<?php

namespace App\Http\Resources\ActuacionesInactivasActuacion;

use Illuminate\Http\Resources\Json\JsonResource;

class ActuacionesInactivasActuacionResource extends JsonResource
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
            "id_actuacion" => $this->resource->id_actuacion,
            //"datos_usuario" => $this->resource->datos_usuario(),
            //"nombre_actuacion" => $this->resource->actuacion(),
            "estado" => $this->resource->estado
        ];
    }
}
