<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParametroCamposFormRequest extends FormRequest
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
            "data.attributes.nombre_campo" => ["required"],
            "data.attributes.type" => ["nullable"],
            "data.attributes.value" => ["nullable"],
            "data.attributes.estado" => ["required"]
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}