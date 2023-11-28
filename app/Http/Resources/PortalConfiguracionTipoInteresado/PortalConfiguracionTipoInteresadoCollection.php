<?php

namespace App\Http\Resources\PortalConfiguracionTipoInteresado;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PortalConfiguracionTipoInteresadoCollection extends ResourceCollection
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
