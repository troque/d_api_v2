<?php

namespace App\Http\Resources\Log\Etapa;

use Illuminate\Http\Resources\Json\JsonResource;

class LogEtapaSimpleResource extends JsonResource
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
            "id_proceso_disciplinario" => (string) $this->resource->getRouteKey(),
            "dependencia" => $this->resource->dependencia_origen,
            "usuario" => $this->resource->created_user
        ];
    }
}
