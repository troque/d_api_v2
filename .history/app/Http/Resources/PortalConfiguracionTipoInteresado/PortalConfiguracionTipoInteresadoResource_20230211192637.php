<?php

namespace App\Http\Resources\PortalConfiguracionTipoInteresado;

use Illuminate\Http\Resources\Json\JsonResource;

class PortalConfiguracionTipoInteresadoResource extends JsonResource
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
            "type" => "portal-tipo-interesado",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "tipo_interesado" => $this->resource->tipo_interesado,
                "nombre_tipo_interesado" => mb_strtoupper($this->resource->tipo_interesado->nombre),
                "tipo_sujeto_procesal" => $this->resource->tipo_sujeto_procesal,
                "permiso_consulta" => $this->resource->permiso_consulta,
                "estado" => $this->resource->estado,
                "fecha_registro" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at))
            ],
            "links" => [
                "self" => url(route("api.v1.portal-tipo-interesado.show", $this->resource)),
            ],
        ];
    }
}
