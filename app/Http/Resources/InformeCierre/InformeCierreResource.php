<?php

namespace App\Http\Resources\InformeCierre;

use Illuminate\Http\Resources\Json\JsonResource;

class InformeCierreResource extends JsonResource
{

    protected $tipo_expediente = null;

    public function tipoExpediente($tipo_expediente){
        $this->tipo_expediente = $tipo_expediente;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "type" => "informe-cierre",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "radicado_sirius" => $this->resource->radicado_sirius,
                "documento_sirius" => $this->resource->documento_sirius,
                "observaciones" => $this->resource->descripcion,
                "fecha_creacion" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "id_fase" => $this->resource->id_fase,
                "id_etapa" => $this->resource->id_etapa,
                "etapa" => $this->resource->etapa,
                "finalizado" => $this->resource->finalizado,
                "documento_soportes" => $this->resource->documentoSoportes,
                "registrado_por" => $this->resource->created_user,
                "tipo_expediente" => $this->tipo_expediente,
                "id_dependencia" => $this->id_dependencia,
                "dependencia" => $this->dependencia,
            ],
            "links" => [
              "self" => url(route("api.v1.evaluacion.show", $this->resource)),
            ],
        ];
    }

}
