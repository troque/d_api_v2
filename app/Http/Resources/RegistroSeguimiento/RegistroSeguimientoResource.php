<?php

namespace App\Http\Resources\RegistroSeguimiento;

use Illuminate\Http\Resources\Json\JsonResource;

class RegistroSeguimientoResource extends JsonResource
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
            "type" => "informe-cierre",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "id_documento_sirius" => $this->resource->id_documento_sirius,
                "documento_sirius" => $this->resource->documentoSoportes,
                "observaciones" => $this->resource->descripcion,
                "fecha_creacion" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "finalizado" => $this->resource->finalizado,
                "registrado_por" => $this->resource->created_user,
            ],
            "links" => [
              "self" => url(route("api.v1.evaluacion.show", $this->resource)),
            ],
        ];
    }
}
