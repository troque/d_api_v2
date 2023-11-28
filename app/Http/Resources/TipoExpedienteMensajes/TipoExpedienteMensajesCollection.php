<?php

namespace App\Http\Resources\TipoExpedienteMensajes;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TipoExpedienteMensajesCollection extends ResourceCollection
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
