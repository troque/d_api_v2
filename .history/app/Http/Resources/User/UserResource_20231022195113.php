<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use \YaLinqo\Enumerable as E;

class UserResource extends JsonResource
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
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id" => (string) $this->resource->getRouteKey(),
                "apellido" => $this->resource->apellido,
                "nombre" => $this->resource->name,
                "identificacion" => $this->resource->identificacion,
                "nombre_completo" => $this->resource->nombre . ' ' . $this->resource->apellido,
                "email" => $this->resource->email,
                "id_dependencia" => $this->resource->id_dependencia,
                "nombre_dependencia" => $this->resource->dependencia,
                "roles" => implode(",", E::from($this->resource->roles)->select(function ($i) {
                    return $i->name;
                })->toArray()),
                "rolesSeparados" => implode(", ", E::from($this->resource->roles)->select(function ($i) {
                    return $i->name;
                })->toArray()),
                "reparto_habilitado" => $this->resource->reparto_habilitado,
                "firma_mecanica" => $this->resource->firma_mecanica,
                "password_firma_mecanica" => $this->resource->password_firma_mecanica,
                "id_mas_grupo_trabajo_secretaria_comun" => $this->resource->id_mas_grupo_trabajo_secretaria_comun,
            ],
            // "links" => [
            //   "self" => url(route("api.v1.user.show", $this->resource)),
            // ],
        ];
    }
}
