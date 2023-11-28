<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntidadInvestigadoFormRequest extends FormRequest
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
            "data.attributes.id_etapa" => ["nullable"],
            "data.attributes.id_entidad" => ["nullable"],
            "data.attributes.nombre_investigado" => ["nullable"],
            "data.attributes.cargo" => ["nullable"],
            "data.attributes.codigo" => ["nullable"],
            "data.attributes.observaciones" => ["nullable"],
            "data.attributes.estado" => ["nullable"],
            "data.attributes.requiere_registro" => ["nullable"],
            "data.attributes.per_page" => ["nullable"],
            "data.attributes.current_page" => ["nullable"],
            "data.attributes.investigado" => ["nullable"],
            "data.attributes.contratista" => ["nullable"],
            "data.attributes.planta" => ["nullable"],
            "data.attributes.comentario_identifica_investigado" => ["nullable"],
            "data.attributes.id_sector" => ["nullable"],

        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
