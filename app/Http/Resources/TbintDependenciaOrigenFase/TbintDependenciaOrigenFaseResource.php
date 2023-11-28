<?php

namespace App\Http\Resources\TbintDependenciaOrigenFase;

use Illuminate\Http\Resources\Json\JsonResource;

class TbintDependenciaOrigenFaseResource extends JsonResource
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
            "attributes" => [
                "id" => (string) $this->resource->getRouteKey(),
                "dependencia" => $this->resource->dependencia_origen,
            ]
        ];
    }
}
