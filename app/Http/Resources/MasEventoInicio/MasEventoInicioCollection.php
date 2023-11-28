<?php

namespace App\Http\Resources\MasEventoInicio;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MasEventoInicioCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
