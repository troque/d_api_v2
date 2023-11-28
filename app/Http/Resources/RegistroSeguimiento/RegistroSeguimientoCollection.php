<?php

namespace App\Http\Resources\RegistroSeguimiento;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RegistroSeguimientoCollection extends ResourceCollection
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
