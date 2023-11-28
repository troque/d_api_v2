<?php

namespace App\Http\Resources\OrdenFuncionario\Historico;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdenFuncionarioHistoricoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "fechas" => $this->fecha_registro,
        ];
    }
}
