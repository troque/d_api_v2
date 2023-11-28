<?php

namespace App\Http\Resources\Antecedente;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Transforma una colleccion en un array
 * @autor: Sandra Saavedra
 * @Fecha:
 */
class AntecedenteCollection extends ResourceCollection
{

    /**
     * Transforma una colleccion en un array
     * @param mixed $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
