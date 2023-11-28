<?php

namespace App\Http\Resources\TempInteresados;

use Illuminate\Http\Resources\Json\JsonResource;

class TempInteresadosResource extends JsonResource
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
            "type" => "temp_interesados",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "tipo_interesado" => $this->resource->tipo_interesado,
                "tipo_sujeto_procesal" => $this->resource->tipo_sujeto_procesal,
                "primer_nombre" => $this->resource->primer_nombre,
                "segundo_nombre" => $this->resource->segundo_nombre,
                "primer_apellido" => $this->resource->primer_apellido,
                "segundo_apellido" => $this->resource->segundo_apellido,
                "tipo_documento" => $this->resource->tipo_documento,
                "numero_documento" => $this->resource->numero_documento,
                "email" => $this->resource->email,
                "telefono" => $this->resource->telefono,
                "telefono2" => $this->resource->telefono2,
                "cargo" => $this->resource->cargo,
                "orientacion_sexual" => $this->resource->orientacion_sexual,
                "sexo" => $this->resource->sexo,
                "direccion" => $this->resource->direccion,
                "departamento" => $this->resource->departamento,
                "ciudad" => $this->resource->ciudad,
                "localidad" => $this->resource->localidad,
                "entidad" => $this->resource->entidad,
                "sector" => $this->resource->sector,
                "radicado" => $this->resource->radicado,
                "vigencia" => $this->resource->vigencia,
                "item" => $this->resource->item,
            ],
            "links" => [
                "self" => url(route("api.v1.temp-interesados.show", $this->resource)),
            ],
        ];
    }
}
