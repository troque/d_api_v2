<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Resources\Json\JsonResource;
use \YaLinqo\Enumerable as E;

class RoleResource extends JsonResource
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
            "type" => "Role",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => $this->resource->name,
                "ids_funcionalidades" => implode(",", E::from($this->resource->funcionalidades)->select(function($i){ return $i->id; })->toArray()),
            ],
            "links" => [
                "self" => url(route("api.v1.role.show", $this->resource)),
            ],
        ];
    }
}
