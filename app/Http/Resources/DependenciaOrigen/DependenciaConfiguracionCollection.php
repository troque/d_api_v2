<?php

namespace App\Http\Resources\DependenciaOrigen;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DependenciaConfiguracionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
