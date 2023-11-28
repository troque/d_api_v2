<?php

namespace App\Http\Resources\ProcesoDiciplinario;

use App\Http\Resources\Antecedente\AntecedenteCollection;
use App\Http\Resources\Antecedente\AntecedenteResource;
use App\Http\Resources\Log\Etapa\LogEtapaSimpleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcesoDiciplinarioResource extends JsonResource
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
                "id_proceso_disciplinario" => (string) $this->resource->getRouteKey(),
                "radicado" => $this->resource->radicado,
                "nombre_origen_radicado" => $this->resource->origenRadicado != null ? $this->resource->origenRadicado->nombre : " ",
                "id_tipo_proceso" => $this->resource->id_tipo_proceso,
                "nombre_tipo_proceso" =>  $this->resource->tipoProceso->nombre,
                //"estado" => $this->resource->getEstado,
                "vigencia" => $this->resource->vigencia,
                "id_etapa" => $this->resource->id_etapa,
                "nombre_etapa" => $this->resource->etapa->nombre,
                /*"proceso_sinproc" => $this->resource->proceso_sinproc,
              "proceso_sirius" => $this->resource->proceso_sirius,
              "proceso_desglose" => $this->resource->proceso_desglose,
              "proceso_poder_preferente" => $this->resource->proceso_poder_preferente,*/
                //"antecedente" => AntecedenteCollection::make($this->resource->antecedente)->last(),
                "antecedente" => $this->resource->antecedente,
                "etapa_log" => LogEtapaSimpleResource::make($this->resource->log_etapa),
                "anexos" => count($this->resource->documentos_sirius),
                "id_funcionario_asignado" =>  $this->resource->id_funcionario_asignado,
                "created_user" => $this->resource->created_user,
                //"usuario" => $this->resource->getUsuarioRegistro,
                "fecha_registro" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "tipo_evaluacion" =>  $this->resource->getTipoEvaluacion($this->resource->getRouteKey()),
                "usuario_comisionado" => $this->resource->getUsuarioComisionado,
                "dependencia_duena" => $this->resource->getDependenciaDuena,

                "radicado_padre" => $this->resource->radicado_padre,
                "vigencia_padre" => $this->resource->vigencia_padre,
            ],
        ];
    }
}
