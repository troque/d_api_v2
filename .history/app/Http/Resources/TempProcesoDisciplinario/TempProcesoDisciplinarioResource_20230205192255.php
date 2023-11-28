<?php

namespace App\Http\Resources\TempProcesoDisciplinario;

use Illuminate\Http\Resources\Json\JsonResource;

class TempProcesoDisciplinarioResource extends JsonResource
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
            "type" => "temp_proceso_disciplinario",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "radicado" => $this->resource->radicado,
                "created_user" => $this->resource->created_user,
                "vigencia" => $this->resource->vigencia,
                "id_vigencia" => $this->resource->idVigencia,
                "registrado_por" => $this->resource->registradoPor == null ? '' : $this->resource->registradoPor->id,
                "estado" => $this->resource->estado,
                "tipo_proceso" => $this->resource->tipoProceso,
                "dependencia_origen" => $this->resource->dependenciaOrigen,
                "dependencia_duena" => $this->resource->dependenciaDuena,
                "etapa" => $this->resource->etapa,
                "id_tipo_expediente" => $this->resource->id_tipo_expediente,
                "id_sub_tipo_expediente" => $this->resource->id_sub_tipo_expediente,
                "id_tipo_evaluacion" => $this->resource->id_tipo_evaluacion,
                "id_tipo_conducta" => $this->resource->id_tipo_conducta,
                "radicado_padre_desglose" => $this->resource->radicado_padre_desglose,
                "vigencia_padre_desglose" => $this->resource->vigencia_padre_desglose,
                "auto_desglose" => $this->resource->auto_desglose,
                "fechaRegistro" => date('Y-m-d', strtotime($this->resource->created_at)),
            ],

            "links" => [
                "self" => url(route("api.v1.temp-proceso-disciplinario.show", $this->resource)),
            ],
        ];
    }
}
