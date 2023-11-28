<?php

namespace App\Http\Resources\DatosInteresado;

use Illuminate\Http\Resources\Json\JsonResource;

class DatosInteresadoResource extends JsonResource
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
            "type" => "interesado",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_etapa" => $this->resource->id_etapa,
                "id_tipo_interesao" => $this->resource->id_tipo_interesao,
                "nombre_tipo_interesado" => $this->resource->tipo_interesado->nombre,
                "id_tipo_sujeto_procesal" => $this->resource->id_tipo_sujeto_procesal,
                "sujeto_procesal_nombre" => $this->resource->sujeto_procesal != null ? $this->resource->sujeto_procesal->nombre : "",
                "tipo_documento" => $this->resource->tipo_documento,
                "nombre_tipo_documento" => strtoupper($this->resource->nombre_tipo_documento),
                "numero_documento" => $this->resource->numero_documento,
                "primer_nombre" => $this->resource->primer_nombre,
                "segundo_nombre" => $this->resource->segundo_nombre,
                "primer_apellido" => $this->resource->primer_apellido,
                "segundo_apellido" => $this->resource->segundo_apellido,
                "id_departamento" => $this->resource->id_departamento,
                "nombre_departamento" => $this->resource->getDepartamento(),
                "id_ciudad" => $this->resource->id_ciudad,
                "nombre_ciudad" => $this->resource->getCiudad(),
                "direccion" => $this->resource->direccion,
                "direccion_json" => $this->resource->direccion_json,
                "id_localidad" => $this->resource->id_localidad,
                "nombre_localidad" => $this->resource->nombre_localidad,
                "email" => $this->resource->email,
                "telefono_celular" => $this->resource->telefono_celular,
                "telefono_fijo" => $this->resource->telefono_fijo,
                "id_sexo" => $this->resource->id_sexo,
                "nombre_sexo" => $this->resource->nombre_sexo,
                "id_genero" => $this->resource->id_genero,
                "nombre_genero" => $this->resource->nombre_genero,
                "id_orientacion_sexual" => $this->resource->id_orientacion_sexual,
                "nombre_orientacion" => $this->resource->nombre_orientacion,
                "entidad" => $this->resource->entidad,
                "cargo" => $this->resource->cargo,
                "cargo_descripcion" => $this->resource->cargo_descripcion,
                "tarjeta_profesional" => $this->resource->tarjeta_profesional,
                "id_dependencia" => $this->resource->id_dependencia,
                "id_tipo_entidad" => $this->resource->id_tipo_entidad,
                "nombre_tipo_entidad" => $this->resource->nombre_tipo_entidad,
                "nombre_entidad" => $this->resource->entidad != null ? $this->resource->entidad : "SIN ENTIDAD",
                "id_entidad" => $this->resource->id_entidad,
                "id_funcionario" => $this->resource->id_funcionario,
                "estado" => $this->resource->estado,
                "nombre_estado" => $this->resource->estado == 1 ? "ACTIVO" : "INACTIVO",
                "folio" => $this->resource->folio,
                "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "created_user" => $this->resource->created_user,
                "nombre_completo" => $this->resource->usuario != null ? $this->resource->usuario->nombre . ' ' . $this->resource->usuario->apellido : "",
                "etapa" => $this->resource->etapa,
                "dependencia" => $this->resource->id_dependencia == 9999 ? "NA" : $this->resource->dependencia,
                "autorizar_envio_correo" => ($this->resource->autorizar_envio_correo == null || $this->resource->autorizar_envio_correo == 0) ? "NO" : "SI",
            ],
            "links" => [
                "self" => url(route("api.v1.datos-interesado.show", $this->resource)),
            ],
        ];
    }
}
