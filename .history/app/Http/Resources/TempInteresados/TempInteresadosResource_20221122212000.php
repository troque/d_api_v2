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
                "tipo_interesado" => $this->resource->tipo_interesado
            ],
            "links" => [
                "self" => url(route("api.v1.temp-interesados.show", $this->resource)),
            ],
        ];
    }
}
