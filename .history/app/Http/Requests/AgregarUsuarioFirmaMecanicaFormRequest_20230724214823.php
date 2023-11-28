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
            "data.attributes.id_actuacion" => ["require"],
            "data.attributes.tipo_firma" => ["require"],
            "data.attributes.uuid_proceso_disciplinario" => ["require"],
            "data.attributes.nombre_documento" => ["require"],
            "data.attributes.ruta_image" => ["require"],
        ];
    }

    /* public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
