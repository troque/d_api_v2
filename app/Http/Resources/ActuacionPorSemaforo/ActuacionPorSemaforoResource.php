<?php

namespace App\Http\Resources\ActuacionPorSemaforo;

use Illuminate\Http\Resources\Json\JsonResource;

class ActuacionPorSemaforoResource extends JsonResource
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
            "type" => "actuacionxsemaforo",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "id_semaforo" => $this->resource->get_id_semaforo,
              "id_interesado" => $this->resource->get_id_interesado,
              "id_actuacion" => $this->resource->get_id_actuacion,
              "observaciones" => $this->resource->observaciones,
              "condiciones" => $this->resource->get_condiciones,
              "fecha_inicio" => $this->resource->fecha_inicio != null ? date("Y-m-d", strtotime($this->resource->fecha_inicio)) : null,
              "fecha_fin" => $this->resource->fecha_fin != null ? date("Y-m-d", strtotime($this->resource->fecha_fin)) : null,
              "finalizo" => $this->resource->finalizo,
              "fechafinalizo" => $this->resource->fechafinalizo != null ? date("Y-m-d", strtotime($this->resource->fechafinalizo)) : null,
              "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
              "created_user" => $this->resource->created_user,
              "updated_user" => empty($this->resource->updated_user) ? "-" : $this->resource->updated_user,
            ],
            "links" => [
              "self" => url(route("api.v1.actuacionxsemaforo.show", $this->resource)),
            ],
        ];
    }
}
