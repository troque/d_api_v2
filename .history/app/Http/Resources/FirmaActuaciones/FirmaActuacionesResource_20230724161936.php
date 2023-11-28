<?php

namespace App\Http\Resources\FirmaActuaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class FirmaActuacionesResource extends JsonResource
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
            "type" => "firma_actuaciones",
            "idw" => $this->resource->getRouteKey(),
            "attributes" => [
                "id_actuacion" => $this->resource->actuacion,
                "nombre_actuacion" => $this->resource->nombreActuacion(),
                "id_actuacion_archivo" => $this->resource->archivo_actuacion,
                "id_user" => $this->resource->usuario,
                "tipo_firma" => $this->resource->tipo_firma,
                "tipo_firma2" => $this->resource->get_tipo_firma,
                "estado" => $this->resource->estado,
                "proceso_disciplinario" => $this->resource->proceso_disciplinario,
                "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "updated_at" => isset($this->resource->updated_at) ? date("d/m/Y h:i:s A", strtotime($this->resource->updated_at)) : "",
            ]
        ];
    }
}
