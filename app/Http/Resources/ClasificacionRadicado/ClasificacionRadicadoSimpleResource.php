<?php

namespace App\Http\Resources\ClasificacionRadicado;

use Illuminate\Http\Resources\Json\JsonResource;

class ClasificacionRadicadoSimpleResource extends JsonResource
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
            "expediente" =>  $this->resource->expediente,
            "tipo_queja" =>  $this->resource->tipo_queja,
        ];
    }
}
