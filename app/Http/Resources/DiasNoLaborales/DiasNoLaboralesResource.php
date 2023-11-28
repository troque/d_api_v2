<?php

namespace App\Http\Resources\DiasNoLaborales;

use Illuminate\Http\Resources\Json\JsonResource;

class DiasNoLaboralesResource extends JsonResource
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
      "type" => "mas_dias_no_laborales",
      "id" => (string) $this->resource->getRouteKey(),
      "attributes" => [
        "fecha" => $this->resource->fecha,
        "estado" => $this->resource->estado,
      ],
      "links" => [
        "self" => url(route("api.v1.mas-dias-no-laborales.show", $this->resource)),
      ],
    ];
  }
}
