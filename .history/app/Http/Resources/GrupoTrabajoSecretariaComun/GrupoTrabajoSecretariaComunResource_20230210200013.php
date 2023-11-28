<?php

namespace App\Http\Resources\GrupoTrabajoSecretariaComun;

use Illuminate\Http\Resources\Json\JsonResource;

class GrupoTrabajoSecretariaComunResource extends JsonResource
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
            "type" => "mas_grupo_trabajo_secretaria_comun",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
                "estado" => $this->resource->estado,
            ],
            "links" => [
                "self" => url(route("api.v1.mas_grupo_trabajo_secretaria_comun.show", $this->resource)),
            ],
        ];
    }
}
