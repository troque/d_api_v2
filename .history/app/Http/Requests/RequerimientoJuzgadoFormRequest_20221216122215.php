<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequerimientoJuzgadoFormRequest extends FormRequest
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
            "data.attributes.id_etapa" => ["nullable"],
            "data.attributes.id_proceso_disciplinario" => ["required"],
            "data.attributes.id_dependencia_origen" => ["nullable"],
            "data.attributes.id_dependencia_destino" => ["nullable"],
            "data.attributes.id_clasificacion_radicado" => ["nullable"],
            "data.attributes.enviar_otra_dependencia" => ["nullable"],
            "data.attributes.id_proceso_disciplinario" => ["nullable"],
            "data.attributes.descripcion" => ["nullable"],
            "data.attributes.created_user" => ["nullable"],
            "data.attributes.id_funcionario_asignado" => ["nullable"],
            "data.attributes.eliminado" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
