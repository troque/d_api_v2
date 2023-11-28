<?php

namespace App\Http\Resources\TipoConducta;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TipoCierreEtapaCollection extends ResourceCollection
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
