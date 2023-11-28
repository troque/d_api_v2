<?php

namespace App\Http\Resources\Sirius;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchRadicadoResource extends JsonResource
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
        "type" => "sirius",
        "id" => (string) $this->resource["trackId"] ?? "",
        "attributes" => [
          "resource" => $this->resource,
        ],
        "links" => [
          "self" => url(route("api.v1.sirius.search-radicado")),
        ],
      ];
    }
}
