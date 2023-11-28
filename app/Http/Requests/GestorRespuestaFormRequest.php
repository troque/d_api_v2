<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GestorRespuestaFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "data.attributes.id_proceso_disciplinario" => ["required"],
            "data.attributes.aprobado" => ["required"],
            "data.attributes.descripcion" => ["required"],
            "data.attributes.version" => ["required"],
            "data.attributes.nuevo_documento" => ["required"],
            "data.attributes.id_tipo_evaluacion" => ["required"],
            "data.attributes.reparto.id_dependencia_origen" => ["nullable"],
            "data.attributes.reparto.id_funcionario_asignado" => ["nullable"],
            "data.attributes.reparto.id_funcionalidad" => ["nullable"],
            "data.attributes.eliminado" => ["nullable"]
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
