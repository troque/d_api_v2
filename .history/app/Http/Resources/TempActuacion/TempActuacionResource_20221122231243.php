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
                "nombre" => $this->resource->nombre,
                "tipo" => $this->resource->tipo,
                "autoNumero" => $this->resource->autonumero,
                "fecha" => $this->resource->fecha,
                "fechaTermino" => $this->resource->fechatermino,
                "instancia" => $this->resource->instancia,
                "decision" => $this->resource->decision,
                "terminoMonto" => $this->resource->terminomonto,
                "observacion" => $this->resource->observacion,
                "radicado" => $this->resource->radicado,
                "vigencia" => $this->resource->vigencia,
                "item" => $this->resource->item,
            ],
            "links" => [
                "self" => url(route("api.v1.temp-actuaciones.show", $this->resource)),
            ],
        ];
    }
}
