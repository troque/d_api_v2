<?php

namespace App\Http\Resources\ConsecutivoDesglose;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsecutivoDesgloseResource extends JsonResource
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
      "type" => "mas_consecutivo_desglose",
      "id" => (string) $this->resource->getRouteKey(),
      "attributes" => [
        "id_vigencia" => $this->resource->getVigencia,
        "consecutivo" => $this->resource->consecutivo,
        "estado" => $this->resource->estado,
      ],
      "links" => [
        "self" => url(route("api.v1.mas_consecutivo_desglose.show", $this->resource)),
      ],
    ];
  }
}
