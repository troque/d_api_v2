<?php

namespace App\Http\Resources\Error;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($error)
    {
        return [
            "estado" => true,
            "error" => $error
        ];
    }
}
