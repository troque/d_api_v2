<?php

namespace App\Http\Resources\ProcesoSiriusDependencia;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcesoSiriusDependenciaResource extends JsonResource
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
