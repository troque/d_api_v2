<?php

namespace App\Http\Resources\PortalDocumentoNotificaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class PortalDocumentoNotificacionesResource extends JsonResource
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
