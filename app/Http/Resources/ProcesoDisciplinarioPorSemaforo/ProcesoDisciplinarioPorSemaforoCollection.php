<?php

namespace App\Http\Resources\ProcesoDisciplinarioPorSemaforo;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProcesoDisciplinarioPorSemaforoCollection extends ResourceCollection
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
