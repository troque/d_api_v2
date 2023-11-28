<?php

namespace App\Http\Resources\Usuario;

use Illuminate\Http\Resources\Json\JsonResource;
use \YaLinqo\Enumerable as E;

class UsuarioRolResource extends JsonResource
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
            "type" => "users_roles",
            "user_id" => $this->resource->user_id,
            "attributes" => [
              "user_id" => $this->resource->user_id,
              "role_id" => $this->resource->roles_id
            ],
            "links" => [
              "self" => url(route("api.v1.users-roles.show", $this->resource)),
            ],
        ];
    }
}



