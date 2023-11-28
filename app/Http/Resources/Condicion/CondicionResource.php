<?php

namespace App\Http\Resources\Condicion;

use Illuminate\Http\Resources\Json\JsonResource;

class CondicionResource extends JsonResource
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
            "type" => "condicion",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "inicial" => $this->resource->inicial,
              "final" => $this->resource->final,
              "color" => $this->resource->color,
              "id_semaforo" => $this->resource->get_id_semaforo,
              "created_user" => $this->resource->created_user,
              "updated_user" => $this->resource->updated_user,
              "deleted_user" => $this->resource->deleted_user,
            ],
            "links" => [
              "self" => url(route("api.v1.condicion.show", $this->resource)),
            ],
        ];
    }
}
