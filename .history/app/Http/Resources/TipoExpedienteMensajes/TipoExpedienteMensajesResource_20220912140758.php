<?php

namespace App\Http\Resources\TipoExpedienteMensajes;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoExpedienteMensajesResource extends JsonResource
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
            "type" => "mas_tipo_expediente_mensajes",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "mensaje" => $this->resource->mensaje,
                "tipo_expediente" => $this->resource->mas_tipo_expediente,
                "id_sub_tipo_expediente" => $this->resource->obtenerInformacionSubTipoExpediente($this->resource->mas_tipo_expediente->id, $this->resource->id_sub_tipo_expediente),
                "estado" => $this->resource->estado,
                "created_at" => $this->resource->created_at,
                "created_user" => $this->resource->created_user,
            ],
            "links" => [
                "self" => url(route("api.v1.mas_tipo_expediente_mensajes.show", $this->resource)),
            ],
        ];
    }
}