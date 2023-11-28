<?php

namespace App\Http\Resources\Fase;

use Illuminate\Http\Resources\Json\JsonResource;

class FaseResource extends JsonResource
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
            "type" => "mas_fase",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
                "estado" => $this->resource->estado,
                "id_etapa" => $this->resource->id_etapa,
                "etapa" => $this->resource->etapa,
                "created_at" =>  date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "created_user" =>  $this->resource->created_user,
            ],
            "links" => [
                "self" => url(route("api.v1.mas-fase.show", $this->resource)),
            ],
        ];
    }
}
