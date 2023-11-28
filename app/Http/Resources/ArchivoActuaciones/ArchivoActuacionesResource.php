<?php

namespace App\Http\Resources\ArchivoActuaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class ArchivoActuacionesResource extends JsonResource
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
            "type" => "archivo-actuaciones",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id" => $this->resource->id,
                "uuid_actuacion" => $this->resource->actuaciones->uuid,
                "id_tipo_archivo" => $this->resource->mas_tipo_archivo_actuaciones->id,
                "nombre_tipo_archivo" => $this->resource->mas_tipo_archivo_actuaciones->nombre,
                "documento_ruta" => $this->resource->documento_ruta,
                "nombre_archivo" => $this->resource->nombre_archivo,
                "extension" => $this->resource->extension,
                "peso" => $this->resource->peso,
            ],
            // "links" => [
            //     "self" => url(route("api.v1.archivo-actuaciones.show", $this->resource)),
            // ],
        ];
    }
}