<?php

namespace App\Http\Resources\Parametro;

use Illuminate\Http\Resources\Json\JsonResource;

class ParametroResource extends JsonResource
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
      "type" => "mas_parametro",
      "id" => (string) $this->resource->getRouteKey(),
      "attributes" => [
        "modulo" => $this->resource->modulo,
        "nombre" => $this->resource->nombre,
        "valor" => $this->resource->valor,
      ],
      "links" => [
        "self" => url(route("api.v1.mas-parametro.show", $this->resource)),
      ],
    ];
  }
}
