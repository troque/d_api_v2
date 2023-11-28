<?php

namespace App\Http\Resources\Fase;

use Illuminate\Http\Resources\Json\JsonResource;

class FaseNombreResource extends JsonResource
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
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
                "link_consulta" => $this->resource->link_consulta_migracion,
                "link_agregar" => $this->resource->link_form_agregar_migracion,
            ],
        ];
    }
}
