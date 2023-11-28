<?php

namespace App\Http\Resources\Usuario;

use Illuminate\Http\Resources\Json\JsonResource;
use \YaLinqo\Enumerable as E;

class UsuarioRolesResource extends JsonResource
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
            "type" => "usuario",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "nombre" => mb_strtoupper($this->resource->nombre),
                "apellido" => mb_strtoupper($this->resource->apellido),
                "name" => !empty($this->resource->name) ? $this->resource->name : "",
                "email" => !empty($this->resource->email) ? $this->resource->email : "",
                "id_dependencia" => $this->resource->id_dependencia,
                "dependencia" => $this->resource->dependencia,
                "estado" => $this->resource->estado,
                "roles" => implode(",", E::from($this->resource->roles)->select(function ($i) {
                    return $i->name;
                })->toArray()),
                "ids_roles" => implode(",", E::from($this->resource->roles)->select(function ($i) {
                    return $i->id;
                })->toArray()),
                "ids_tipo_expediente" => $this->resource->ids_tipo_expediente,
                "reparto_habilitado" => $this->resource->reparto_habilitado,
                "firma_mecanica" => $this->resource->firma_mecanica,
                "id_mas_grupo_trabajo_secretaria_comun" => $this->resource->id_mas_grupo_trabajo_secretaria_comun,
            ],
            "links" => [
                "self" => url(route("api.v1.usuario.show", $this->resource)),
            ],
        ];
    }
}
