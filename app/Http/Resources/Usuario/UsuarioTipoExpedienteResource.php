<?php

namespace App\Http\Resources\Usuario;

use Illuminate\Http\Resources\Json\JsonResource;
use \YaLinqo\Enumerable as E;

class UsuarioTipoExpedienteResource extends JsonResource
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
            "type" => "users_tipo_expediente",
            "user_id" => $this->resource->user_id,
            "attributes" => [
              "user_id" => $this->resource->user_id,
              "tipo_expediente_id" => $this->resource->tipo_expediente_id,
              "sub_tipo_expediente_id" => $this->resource->sub_tipo_expediente_id
            ],
            "links" => [
              "self" => url(route("api.v1.users-tipo-expediente.show", $this->resource)),
            ],
        ];
    }
}



