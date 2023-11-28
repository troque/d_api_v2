<?php

namespace App\Http\Resources\Error;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ErrorCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "estado" => true,
            "error" => $error
        ];;
    }
}
