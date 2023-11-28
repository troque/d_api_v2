<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TempAntecedentesFormRequest extends FormRequest
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
            "data.attributes.descripcion" => ["required"],
            "data.attributes.fecha_registro" => ["required"],
            "data.attributes.id_temp_proceso_disciplinario" => [""],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
