<?php

namespace App\Http\Resources\LogAntecedente;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LogAntecedenteCollection extends ResourceCollection
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
