<?php

namespace App\Http\Resources\TipoEstadoEtapa;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TipoEstadoEtapaCollection extends ResourceCollection
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
