<?php

namespace App\Http\Resources\Entidad;

use Illuminate\Http\Resources\Json\JsonResource;

class EntidadResource extends JsonResource
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
            "type" => "entidad",
            "id" => $this->resource->identidad,
            "nombre" => $this->resource->nombre,
            "direccion" => $this->resource->direccion,
            "nombre_secretaria" => $this->resource->nombre_secretaria,
            "nombre_sector" => $this->resource->nombre_sector,
            "paginaweb" => $this->resource->paginaweb,
            "correo" => $this->resource->correo,
            "telefono" => $this->resource->telefono,
            "codigopostal" => $this->resource->codigopostal
            /*,
            "links" => [
              "self" => url(route("api.v1.entidad.show", $this->resource)),
            ],*/
        ];
    }
}
