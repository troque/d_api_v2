<?php

namespace App\Http\Resources\DocumentoSiriusNombre;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentoSiriusNombreResource extends JsonResource
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
            "type" => "documento_sirius",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre_archivo" => $this->resource->nombre_archivo,
                "sirius_track_id" => $this->resource->sirius_track_id,
            ]
        ];
    }
}
