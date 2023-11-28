<?php

namespace App\Http\Resources\ProcesoDisciplinarioPorSemaforo;

use Adldap\Utilities;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcesoDisciplinarioPorSemaforoResource extends JsonResource
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
            "type" => "pdxsemaforo",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_semaforo" => $this->resource->get_id_semaforo,
                "id_proceso_disciplinario" => $this->resource->get_id_proceso_disciplinario,
                "id_actuacion" => $this->resource->get_id_actuacion,
                "actuacion" => $this->resource->get_id_actuacion ? $this->resource->get_actuacion($this->resource->get_id_actuacion->id_actuacion) : null,
                "condiciones" => $this->resource->get_condiciones,
                "fecha_inicio" => Utilities->getFormatoFechaDDMMYY($this->resource->fecha_inicio),
                "created_user" => $this->resource->created_user,
                "updated_user" => $this->resource->updated_user,
                "deleted_user" => $this->resource->deleted_user,
                "finalizo" => $this->resource->finalizo,
                "fechafinalizo" => $this->resource->fechafinalizo != null ? date("Y-m-d", strtotime($this->resource->fechafinalizo)) : null,
                "motivo_finalizado" => $this->resource->motivo_finalizado($this->resource->id_actuacion_finaliza, $this->resource->id_dependencia_finaliza, $this->resource->id_usuario_finaliza),
            ],
            "links" => [
                "self" => url(route("api.v1.pdxsemaforo.show", $this->resource)),
            ],
        ];
    }
}
