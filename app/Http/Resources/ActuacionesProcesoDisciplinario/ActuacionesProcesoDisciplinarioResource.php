<?php

namespace App\Http\Resources\ActuacionesProcesoDisciplinario;

use Illuminate\Http\Resources\Json\JsonResource;

class ActuacionesProcesoDisciplinarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $firmas = $this->resource->firmas($this->resource->getRouteKey(), $this->resource->auto, $this->resource->archivos($this->resource->getRouteKey()));
        $mas_actuacion = $this->resource->mas_actuaciones;
        $etapa_siguiente = $this->resource->id_etapa_siguiente ? $this->resource->etapa_siguiente($this->resource->id_etapa_siguiente) : null;
        $estado_actuacion = $this->resource->mas_estado_actuaciones;
        $actuacion_inactiva_principal = $this->resource->actuacion_inactiva_principal($this->resource->getRouteKey());
        $proceso_disciplinario = $this->resource->proceso_disciplinario;
        $dependenciaSegundaInstancia = $this->resource->dependenciaSegundaInstancia();

        return [
            "type" => "actuaciones",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_actuacion" => $this->resource->id_actuacion,
                "mas_actuacion" => $mas_actuacion,
                "estado_actuacion" => $estado_actuacion,
                "etapa" => $this->resource->etapa,
                "etapa_siguiente" => $etapa_siguiente,
                "dependencia_creadora" => $this->resource->dependencia,
                "usuario" => $this->resource->usuarioDatosEspecificos,
                "documento_ruta" => $this->resource->documento_ruta,
                "archivos" => $this->resource->archivos($this->resource->getRouteKey()),
                "auto" => $this->resource->auto,
                "campos_finales" => $this->resource->campos_finales != null ? json_decode($this->resource->campos_finales) : "",
                "estado" => $this->resource->estado,
                "id_estado_visibilidad" => $this->resource->id_estado_visibilidad,
                "incluir_reporte" => $this->resource->incluir_reporte,
                "actuacion_inactiva_principal" => $actuacion_inactiva_principal,
                "created_user" => $this->resource->created_user,
                "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "updated_at" => isset($this->resource->updated_at) ? date("d/m/Y h:i:s A", strtotime($this->resource->updated_at)) : "",
                "acciones" => $this->resource->acciones($estado_actuacion->codigo, $mas_actuacion, $etapa_siguiente, $firmas, $actuacion_inactiva_principal, $proceso_disciplinario, $dependenciaSegundaInstancia),
                "firmas" => $firmas,
                "trazabilidad_primer_registro" => $this->resource->trazabilidadPrimerRegistro($this->resource->getRouteKey()),
                "fecha_registro" => $this->resource->fecha_registro ? date("d/m/Y", strtotime($this->resource->fecha_registro)) : date("d/m/Y"),
            ],
            "links" => [
                "self" => url(route("api.v1.actuaciones.show", $this->resource)),
            ],
        ];
    }
}
