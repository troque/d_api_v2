<?php

namespace App\Http\Resources\TempActuaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class TempActuacionesResource extends JsonResource
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
            "type" => "temp_actuaciones",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "radicado" => $this->resource->radicado,
                "vigencia" => $this->resource->radicado,
                "item" => $this->resource->item,
                "nombre" => $this->resource->nombre,
                "tipo" => $this->resource->tipo,
                "autoNumero" => $this->resource->autonumero,
                "fecha" => date('Y-m-d',strtotime($this->resource->fecha)),
                "path" => $this->resource->path,
            ],
            "links" => [
                "self" => url(route("api.v1.temp-actuaciones.show", $this->resource)),
            ],
        ];
    }
}
