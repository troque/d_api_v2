<?php

namespace App\Http\Resources\Sirius;

use Illuminate\Http\Resources\Json\JsonResource;

class RadicacionResource extends JsonResource
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
        "id" => (string) $this->resource["trackId"],
        "attributes" => [
          "trackId" => $this->resource["trackId"],
        ],
        "links" => [
          "self" => url(route("api.v1.sirius.radicacion")),
        ],
      ];
    }
}
