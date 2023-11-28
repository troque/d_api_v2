<?php

namespace App\Http\Resources\GestorRespuesta;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GestorRespuestaCollection extends ResourceCollection
{
    protected $rol_siguiente;
    protected $rol_anterior;
    protected $rol_actual;
    protected $evaluacion;
    protected $rol_previo;
    protected $tipo_expediente = null;

    public function rolSiguiente($rol_siguiente){
        $this->rol_siguiente = $rol_siguiente;
        return $this;
    }

    public function rolAnterior($rol_anterior){
        $this->rol_anterior = $rol_anterior;
        return $this;
    }

    public function rolActual($rol_actual){
        $this->rol_actual = $rol_actual;
        return $this;
    }

    public function evaluacion($evaluacion){
        $this->evaluacion = $evaluacion;
        return $this;
    }

    public function rolPrevio($rol_previo){
        $this->rol_previo = $rol_previo;
        return $this;
    }

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
    /*public function toArray($request)
    {
        return parent::toArray($request);
    }*/

    public function toArray($request){
        return $this->collection->map(function(GestorRespuestaResource $resource) use($request){
            return $resource->rolSiguiente($this->rol_siguiente)->rolAnterior($this->rol_anterior)->rolActual($this->rol_actual)->evaluacion($this->evaluacion)->rolPrevio($this->rol_previo)->tipoExpediente($this->tipo_expediente)->toArray($request);
        })->all();
    }

}
