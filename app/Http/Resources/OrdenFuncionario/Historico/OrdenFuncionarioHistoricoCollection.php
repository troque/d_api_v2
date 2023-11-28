<?php

namespace App\Http\Resources\OrdenFuncionario\Historico;

use App\Http\Resources\OrdenFuncionario\Historico\OrdenFuncionarioHistoricoResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrdenFuncionarioHistoricoCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "data_fecha" => $this->collection,
        ];
    }
}
