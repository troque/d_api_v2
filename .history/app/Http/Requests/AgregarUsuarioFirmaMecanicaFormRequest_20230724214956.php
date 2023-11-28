<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgregarUsuarioFirmaMecanicaFormRequest extends FormRequest
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
            "data.attributes.id_actuacion" => ["nullable"],
            "data.attributes.id_user" => ["nullable"],
            "data.attributes.tipo_firma" => ["nullable"],
            "data.attributes.estado" => ["nullable"],
            "data.attributes.uuid_proceso_disciplinario" => ["nullable"],
            "data.attributes.nombre_documento" => ["nullable"],
            "data.attributes.ruta_image" => ["nullable"],
        ];
    }

    /* public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
