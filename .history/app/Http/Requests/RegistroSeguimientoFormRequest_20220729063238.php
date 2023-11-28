<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistroSeguimientoFormRequest extends FormRequest
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
            "data.attributes.id_documento_sirius" => ["nullable"],
            "data.attributes.descripcion" => ["required"],
            "data.attributes.fecha_registro" => ["required"],
            "data.attributes.finalizado" => ["required"],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
