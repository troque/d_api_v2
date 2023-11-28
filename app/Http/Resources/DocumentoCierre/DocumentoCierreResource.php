<?php

namespace App\Http\Resources\DocumentoCierre;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentoCierreResource extends JsonResource
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
            "type" => "documento_cierre",
            "id" => (string) $this->resource->getRouteKey(),
            "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
            "seguimiento" => $this->resource->seguimiento,
            "descripcion_seguimiento" => $this->resource->descripcion_seguimiento,
            "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at))
        ];
    }
}
