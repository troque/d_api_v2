<?php

namespace App\Http\Resources\OrdenFuncionario;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdenFuncionarioResource extends JsonResource
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
            "id_lista_funcionarios" => (string) $this->resource->getRouteKey(),
            "id" => $this->id_funcionario,
            "orden" => $this->resource->orden,
            "nombre" => $this->id_funcionario > 0 ? $this->resource->rol->name : null,
            "grupo" => $this->resource->grupo,
            "unico_rol" => $this->resource->unico_rol,
            "id_evaluacion" => $this->resource->id_evaluacion,
            "id_expediente" => $this->resource->id_expediente,
            "id_sub_expediente" => $this->resource->id_sub_expediente,
            "id_tercer_expediente" => $this->resource->id_tercer_expediente,
            "fecha_registro" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
            "creado_por" => $this->resource->user->nombre . ' ' . $this->resource->user->apellido
        ];
    }
}
