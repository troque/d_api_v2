<?php

namespace App\Http\Resources\AutoFinalizas;

use Illuminate\Http\Resources\Json\JsonResource;

class AutoFinalizaResource extends JsonResource
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
            "type" => "auto_finaliza",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "id_semaforo" => $this->resource->get_id_semaforo,
              "id_mas_actuacion" => $this->resource->get_id_mas_actuacion,
              "id_etapa" => $this->resource->get_id_etapa,
              "created_user" => $this->resource->created_user,
              "updated_user" => $this->resource->updated_user,
              "deleted_user" => $this->resource->deleted_user,
            ],
            "links" => [
              "self" => url(route("api.v1.auto_finaliza.show", $this->resource)),
            ],
        ];
    }
}
