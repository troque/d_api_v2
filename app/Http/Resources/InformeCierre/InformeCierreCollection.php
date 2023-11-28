<?php

namespace App\Http\Resources\InformeCierre;

use Illuminate\Http\Resources\Json\ResourceCollection;

class InformeCierreCollection extends ResourceCollection
{

    protected $tipo_expediente = null;

    public function tipoExpediente($tipo_expediente){
        $this->tipo_expediente = $tipo_expediente;
        return $this;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request){
        return $this->collection->map(function(InformeCierreResource $resource) use($request){
            return $resource->tipoExpediente($this->tipo_expediente)->toArray($request);
        })->all();
    }
}
