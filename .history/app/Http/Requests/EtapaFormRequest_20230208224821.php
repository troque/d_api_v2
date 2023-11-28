<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EtapaFormRequest extends FormRequest
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
            "data.attributes.estado" => ["required"],
            "data.attributes.id_tipo_proceso" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
