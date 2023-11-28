<?php

namespace App\Http\Resources\GestorRespuesta;

use App\Http\Resources\ClasificacionRadicado\ClasificacionRadicadoResource;
use App\Http\Resources\ClasificacionRadicado\ClasificacionRadicadoSimpleResource;
use App\Http\Utilidades\Utilidades;
use Illuminate\Http\Resources\Json\JsonResource;

class GestorRespuestaResource extends JsonResource
{
    protected $rol_siguiente = null;
    protected $rol_anterior = null;
    protected $rol_actual = null;
    protected $rol_seleccionado = null;
    protected $evaluacion = null;
    protected $rol_previo = null;
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

    public function rolSeleccionado($rol_seleccionado){
        $this->rol_seleccionado = $rol_seleccionado;
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
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $usuario_actual = false;

        if($this->rol_siguiente){
            if(isset($this->rol_siguiente->nombre_funcionario) && $this->rol_siguiente->nombre_funcionario === auth()->user()->name){
                $usuario_actual = true;
            }
        }
        else if($this->rol_seleccionado){
            if(isset($this->rol_seleccionado->nombre_funcionario) && $this->rol_seleccionado->nombre_funcionario === auth()->user()->name){
                $usuario_actual = true;
            }
        }

        return [
            "type" => "gestor_respuesta",
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => [
                "id_proceso_disciplinario" => $this->resource->id_proceso_disciplinario,
                "version" => $this->resource->version,
                "nuevo_documento" => $this->resource->nuevo_documento,
                "aprobado" => $this->resource->aprobado,
                "proceso_finalizado" => $this->resource->proceso_finalizado,
                "descripcion" => $this->resource->descripcion,
                "descripcion_corta" => Utilidades::getDescripcionCorta($this->resource->descripcion),
                //"descripcion_corta" => Utilidades::getDescripcionCortaWithLenght($this->resource->descripcion, 100),
                "documento_sirius" => $this->resource->documentoSirius(),
                "orden_funcionario" => $this->resource->orden_funcionario,
                "clasificacion_expediente" => ClasificacionRadicadoSimpleResource::make($this->resource->clasificacionExpediente),
                "rol_siguiente" => $this->rol_siguiente,
                "rol_anterior" => $this->rol_anterior,
                "rol_actual" => $this->rol_actual,
                "rol_seleccionado" => $this->rol_seleccionado,
                "usuario" => $this->usuario->nombre . ' ' . $this->usuario->apellido,
                "usuario_actual" => $usuario_actual,
                "rol_previo" => $this->rol_previo,
                "evaluaciones" => $this->evaluacion,
                "fecha" => date("d/m/Y h:i:s A", strtotime($this->resource->created_at)),
                "tipo_expediente" => $this->tipo_expediente,
                "grupo" => $this->resource->id_mas_orden_funcionario,
            ],
            "links" => [
              "self" => url(route("api.v1.mas-formato.show", $this->resource)),
            ],
        ];
    }

    public static function collection($resource){
        return new GestorRespuestaCollection($resource);
    }
}
