<?php

namespace App\Http\Resources\ProcesoDesgloseDependencia;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcesoDesgloseDependenciaResource extends JsonResource
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
            "id" => (string) $this->resource->getRouteKey(),
            "dependencia" => $this->resource->dependencia_origen,
            "id_tramite_usuario" => $this->resource->id_tramite_usuario,
            "fecha_ingreso" => $this->resource->fecha_ingreso,
            "numero_auto" => $this->resource->numero_auto,
            "auto_asociado" => $this->resource->auto_asociado,
            "fecha_auto_desglose" => $this->resource->fecha_auto_desglose
        ];
    }
}
