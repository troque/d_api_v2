<?php

namespace App\Http\Resources\DocumentoSirius;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentoSiriusResource extends JsonResource
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
              "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
              "id_etapa" => $this->resource->id_etapa,
              "id_fase" => $this->resource->id_fase,
              "url_archivo" => $this->resource->url_archivo,
              "nombre_archivo" => $this->resource->nombre_archivo,
              "estado" => $this->resource->estado,
              "num_folios" => $this->resource->num_folios,
              "num_radicado" => $this->resource->num_radicado,
              "extension" => $this->resource->extension,
              "peso" => $this->resource->peso,
              "etapa" =>  $this->resource->etapa,
              "fase" =>  $this->resource->fase,
              "grupo" =>  $this->resource->grupo,
              "created_user" =>  $this->resource->created_user,
              "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
            ],
            "links" => [
              "self" => url(route("api.v1.documento-sirius.show", $this->resource)),
            ],
        ];
    }
}
