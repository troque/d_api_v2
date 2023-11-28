<?php

namespace App\Http\Resources\DependenciaOrigen;

use Illuminate\Http\Resources\Json\JsonResource;
use \YaLinqo\Enumerable as E;

class DependenciaConfiguracionResource extends JsonResource
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
      "type" => "mas_dependencia_configuracion",
      "attributes" => [
        "id_dependencia_origen" => $this->resource->id_dependencia_origen,
        "id_dependencia_acceso" => $this->resource->id_dependencia_acceso,
        "estado" => $this->resource->estado        
      ],
    
    ];
  }
}
