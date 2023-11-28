<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstadoActuacionesFormRequest extends FormRequest
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
            "data.attributes.nombre" => ["required"],
            "data.attributes.codigo" => ["required"],
            "data.attributes.descripcion" => ["required"],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}