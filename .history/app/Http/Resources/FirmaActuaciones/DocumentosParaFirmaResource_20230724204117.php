<?php

namespace App\Http\Resources\FirmaActuaciones;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentosParaFirmaResource extends JsonResource
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
            "type" => "firma_actuaciones",
            "id" => $this->resource->getRouteKey(),
            "attributes" => [
                "id_actuacion" => $this->resource->actuacion->uuid,
                "nombre_actuacion" => $this->resource->nombreActuacion(),
                "etapa" => $this->resource->nombreEtapa(),
                "documento_ruta" => $this->resource->actuacion->documento_ruta,
                "fecha_creacion" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "radicado" => $this->resource->proceso_disciplinario->radicado,
                "vigencia" => $this->resource->proceso_disciplinario->vigencia,
                "usuario_solicita_firma" => $this->resource->usuarioSolicitaFirma(),
                "tipo_firma" => $this->resource->get_tipo_firma->nombre,
                "firma_mencanica" => $this->resource->usuario->firma_mecanica,
            ]
        ];
    }
}
