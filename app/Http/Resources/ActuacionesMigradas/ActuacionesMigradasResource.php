<?php

namespace App\Http\Resources\ActuacionesMigradas;

use Illuminate\Http\Resources\Json\JsonResource;

class ActuacionesMigradasResource extends JsonResource
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
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "radicado" => $this->resource->radicado,
                "vigencia" => $this->resource->vigencia,
                "item" => $this->resource->item,
                "nombre" => $this->resource->nombre,
                "id_tipo_actuacion" => $this->resource->id_tipo_actuacion,
                "id_etapa" => $this->resource->id_etapa,
                "auto" => $this->resource->autonumero,
                "fecha" => $this->resource->fecha,
                "path" => $this->resource->path,
                "dependencia" => $this->resource->mas_dependencia_origen,
                "usuario" => $this->resource->usuario,
                "actuacion" => $this->resource->mas_actuaciones,
                "created_user" => $this->resource->created_user,
                "archivo_pdf" => $this->resource->archivo_pdf,
                "estado" => $this->resource->estado(),
            ],
            "links" => [
                "self" => url(route("api.v1.actuaciones.show", $this->resource)),
            ],
        ];
    }
}
