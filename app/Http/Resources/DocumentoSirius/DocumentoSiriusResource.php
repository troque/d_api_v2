<?php

namespace App\Http\Resources\DocumentoSirius;

use App\Http\Utilidades\Utilidades;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentoSiriusResource extends JsonResource
{

    protected $documento_cierre = null;

    public function documentoCierre($documento_cierre){
        $this->documento_cierre = $documento_cierre;
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
            "type" => "documento_sirius",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
              "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
              "id_etapa" => $this->resource->id_etapa,
              "id_fase" => $this->resource->id_fase,
              "url_archivo" => $this->resource->url_archivo,
              "nombre_archivo" => $this->resource->nombre_archivo,
              "estado" => $this->resource->estado,
              "num_folios" => $this->resource->num_folios,
              "num_radicado" => $this->resource->num_radicado,
              "extension" => $this->resource->extension,
              "peso" => $this->resource->peso,
              "etapa" =>  $this->resource->etapa,
              "fase" =>  $this->resource->fase,
              "grupo" =>  $this->resource->grupo,
              "compulsa" =>  $this->resource->es_compulsa,
              "id_mas_formato" =>  $this->resource->id_mas_formato,
              "path" =>  $this->resource->path,
              "created_user" => $this->resource->created_user,
              "descripcion" => $this->resource->descripcion->descripcion,
              "descripcion_corta" => Utilidades::getDescripcionCorta($this->resource->descripcion->descripcion),
              "nombre_completo" => $this->resource->usuario!=null?$this->resource->usuario->nombre .' '.$this->resource->usuario->apellido : "",
              "created_at" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
              "id_log_proceso_disciplinario" => $this->resource->id_log_proceso_disciplinario,
              "sirius_track_id" => $this->resource->sirius_track_id,
              "sirius_ecm_id" => $this->resource->sirius_ecm_id,
              "documento_cierre" => $this->documento_cierre,
              "usuario" => $this->resource->getDependenciaUsuario($this->resource->usuario->id_dependencia),
            ],
            "links" => [
              "self" => url(route("api.v1.documento-sirius.show", $this->resource)),
            ],
        ];
    }
}
