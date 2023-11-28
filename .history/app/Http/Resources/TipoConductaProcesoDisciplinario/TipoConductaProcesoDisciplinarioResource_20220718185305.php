<?php

namespace App\Http\Resources\TipoConductaProcesoDisciplinario;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoConductaProcesoDisciplinarioResource extends JsonResource
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
            "type" => "tipo_conducta_proceso_disciplinario",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "uuid" => $this->resource->uuid,
              "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
              "id_tipo_conducta" => $this->resource->id_tipo_conducta,
              "estado" => $this->resource->estado,
              "id_etapa" => $this->resource->id_etapa,
              "created_user" => $this->resource->created_user,
              "updated_user" => $this->resource->updated_user,
              "deleted_user" => $this->resource->deleted_user
            ],
            "links" => [
              "self" => url(route("api.v1.tipo-conducta-proceso-disciplinario.show", $this->resource)),
            ],
        ];
    }
}
