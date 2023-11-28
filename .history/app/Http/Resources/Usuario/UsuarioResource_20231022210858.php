<?php

namespace App\Http\Resources\Usuario;

use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
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
                "nombre" => !empty($this->resource->nombre) ? mb_strtoupper($this->resource->nombre) : "",
                "apellido" => !empty($this->resource->apellido) ? mb_strtoupper($this->resource->apellido) : "",
                "name" => !empty($this->resource->name) ? $this->resource->name : "",
                "email" => !empty($this->resource->email) ? $this->resource->email : "",
                "identificacion" => !empty($this->resource->identificacion) ? $this->resource->identificacion : "",
                "id_dependencia" => !empty($this->resource->id_dependencia) ? $this->resource->id_dependencia : "",
                "grupo_trabajo_secretaria_comun" => !empty($this->resource->id_mas_grupo_trabajo_secretaria_comun) ?  $this->resource->id_mas_grupo_trabajo_secretaria_comun : "",
                "nombre_dependencia" => !empty($this->resource->dependencia->nombre) ? $this->resource->dependencia->nombre : "",
                "dependencia" => !empty($this->resource->dependencia) ? $this->resource->dependencia : "",
                "nombre_estado" => $this->resource->estado ? 'ACTIVO' : 'INACTIVO',
                "estado" => $this->resource->estado,
                "reparto_habilitado" => $this->resource->reparto_habilitado,
                "firma_mecanica" => !empty($this->resource->firma_mecanica) ? $this->resource->firma_mecanica : "",
                "password_firma_mecanica" => !empty($this->resource->password_firma_mecanica) ? $this->resource->password_firma_mecanica : "",
            ],
            "links" => [
                "self" => url(route("api.v1.usuario.show", $this->resource)),
            ],
        ];
    }
}
