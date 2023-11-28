<?php

namespace App\Http\Resources\Etapa;

use Illuminate\Http\Resources\Json\JsonResource;

class EtapaResource extends JsonResource
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
            "type" => "mas_etapa",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => $this->resource->nombre,
                "estado" => $this->resource->estado,
                "id_tipo_proceso" => $this->resource->id_tipo_proceso,
                "tipo_proceso" => $this->resource->tipo_proceso,
                "created_at" =>  date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "created_user" =>  $this->resource->created_user,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-etapa.show", $this->resource)),
            ],
        ];
    }
}
