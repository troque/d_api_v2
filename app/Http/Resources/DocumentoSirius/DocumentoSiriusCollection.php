<?php

namespace App\Http\Resources\DocumentoSirius;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DocumentoSiriusCollection extends ResourceCollection
{

    protected $documento_cierre = null;

    public function documentoCierre($documento_cierre){
        $this->documento_cierre = $documento_cierre;
        return $this;
    }


    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request){
        return $this->collection->map(function(DocumentoSiriusResource $resource) use($request){
            return $resource->documentoCierre($this->documento_cierre)->toArray($request);
        })->all();
    }
}
