<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * [Description AntecedenteFormRequest]
 */
class AntecedenteFormRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer este request
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Se define las reglas de valicación del request
     *
     * @return [type]
     */
    public function rules()
    {
        return [
            "data.attributes.fecha_registro" => ["nullable"],
            "data.attributes.id_dependencia" => ["nullable"],
            "data.attributes.descripcion" => ["nullable"],
            "data.attributes.estado" => ["nullable"],
            "data.attributes.estado_observacion" => ["nullable"],
            "data.attributes.id_proceso_disciplinario" => ["nullable"],
            "data.attributes.created_user" => [""],
            "data.attributes.per_page" => ["nullable"],
            "data.attributes.current_page" => ["nullable"]
        ];
    }


    /**
     * Retona las estructura de la cadena json del request
     * @return [type]
     */
    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
