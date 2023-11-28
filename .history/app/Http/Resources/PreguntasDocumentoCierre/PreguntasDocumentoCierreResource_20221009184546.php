<?php

namespace App\Http\Resources\PreguntasDocumentoCierre;

use Illuminate\Http\Resources\Json\JsonResource;

class PreguntasDocumentoCierreResource extends JsonResource
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
            "type" => "mas_preguntas_doc_cierre",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => $this->resource->nombre,
            ],
            "links" => [
                "self" => url(route("api.v1.mas-preguntas-documento-cierre.show", $this->resource)),
            ],
        ];
    }
}
