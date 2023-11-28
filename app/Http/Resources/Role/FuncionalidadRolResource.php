<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Resources\Json\JsonResource;
use \YaLinqo\Enumerable as E;

class FuncionalidadRolResource extends JsonResource
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
            "type" => "FUNCIONALIDAD_ROL",
            "attributes" => [
              "FUNCIONALIDAD_ID" => $this->resource->FUNCIONALIDAD_ID,
              "ROLE_ID" => $this->resource->ROLE_ID
            ],
            "links" => [
              "self" => url(route("api.v1.funcionalidad-rol.show", $this->resource)),
            ],
        ];
    }
}



