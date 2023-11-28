<?php

namespace App\Http\Resources\LogProcesoDisciplinario;

use Illuminate\Http\Resources\Json\JsonResource;

class LogProcesoDisciplinarioResource extends JsonResource
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
            "type" => "log_proceso_disciplinario",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_proceso_disciplinario"=> $this->resource->id_proceso_disciplinario,
                "id_tipo_log" => $this->resource->id_tipo_log,
                "id_estado" => $this->resource->id_estado,
                "descripcion" => $this->resource->descripcion,
                "descripcion_corta" => $this->resource->getDescripcionCorta(),
                "id_dependencia_origen"=>$this->resource->id_dependencia_origen,
                "etapa" => $this->resource->etapa,
                "fase" => $this->resource->fase,
                "dependencia_origen" => $this->resource->dependencia_origen,
                "estado_etapa" => $this->resource->estado_etapa,
                "tipo_log" => $this->resource->tipo_log,
                "funcionario" => $this->resource->created_user,
                "documentos" => $this->resource->documentos,
                "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "created_user" => $this->resource->created_user,
                "nombre_completo" => $this->resource->usuario!=null?$this->resource->usuario->nombre .' '.$this->resource->usuario->apellido : "",
                "funcionario_actual" => $this->resource->funcionario_actual,
                "funcionario_registra" => $this->resource->funcionario_registra,
                "dep_funcionario_actual" => $this->resource->funcionario_actual == null ? "" : $this->resource->funcionario_actual->dependencia,
                "dep_funcionario_registra" => $this->resource->funcionario_registra == null ? "" : $this->resource->funcionario_registra->dependencia,
                "tipo_trasaccion" => $this->resource->tipo_transaccion,
                "clasificacion_radicado"=> $this->resource->id_clasificacion_radicado,
                "funcionario_asignado" => $this->resource->funcionario_asignado,
            ],
            "links" => [
              "self" => url(route("api.v1.log-proceso-disciplinario.show", $this->resource)),
            ],
        ];
    }
}
