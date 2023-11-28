<?php

namespace App\Http\Resources\MisPendientes;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Http\Utilidades\Utilidades;

class MisPendientesResource extends JsonResource
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
            "type" => "proceso_disciplinario",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_tipo_proceso" => $this->resource->id_tipo_proceso,
                "nombre_tipo_expediente" => $this->resource->tipoProceso->nombre,
                "radicado" => $this->resource->radicado,
                "proceso" => $this->resource->nombre,
                "vigencia" => $this->resource->vigencia,
                "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "contador_no_laborales" => $this->resource->contador_no_laborales,
                "dias_habiles" => $this->resource->getDiasHabiles(),
                "dias_calendario" => $this->resource->getDiasCalendario(),
                "tipo_expediente" => $this->resource->getTipoExpediente,
                "ultimo_antecedente" => $this->resource->antecedente,
                "ultima_clasificacion" => $this->resource->ultima_clasificacion,
                "proceso_sinproc" => $this->resource->proceso_sinproc,
                "proceso_desglose" => $this->resource->proceso_desglose,
                "proceso_poder_preferente" => $this->resource->proceso_poder_preferente,
                "proceso_sirius" => $this->resource->proceso_sirius,
                "usuario" => $this->resource->antecedente->usuario,
                "dependencia_registro" => $this->resource->antecedente->dependencia,
                "evaluacion" => $this->resource->getTipoEvaluacion($this->resource->getRouteKey()),
                "estado" => $this->resource->getEstado

                //"dias_habiles2" => Utilidades::getNumeroDiasEntreFechas($this->resource->created_at),
                //"dias_calendario2" => Utilidades::getNumeroDiasCalendario($this->resource->created_at, $this->resource->contador_no_laborales),
            ],
            "links" => [
                "self" => url(route("api.v1.mis-pendientes.show", $this->resource)),
            ],
        ];
    }
}
