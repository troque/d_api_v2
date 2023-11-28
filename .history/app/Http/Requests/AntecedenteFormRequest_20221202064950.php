<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * [Description AntecedenteFormRequest]
 */
class AntecedenteFormRequest extends FormRequest
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
     * Se define las reglas de valicación del request
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

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
