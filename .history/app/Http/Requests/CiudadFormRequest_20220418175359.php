<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CiudadFormRequest extends FormRequest
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
            "data.attributes.nombre" => ["required", "min:1", "max:35"],
            "data.attributes.codigo_dane" => ["required"],
            "data.attributes.id_departamento" => ["required"],
            "data.attributes.estado" => ["nullable"],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
