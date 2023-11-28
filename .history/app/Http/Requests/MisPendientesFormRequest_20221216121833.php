<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MisPendientesFormRequest extends FormRequest
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
            "data.attributes.nombre" => ["nullable", "min:1", "max:35"],
            "data.attributes.codigo_dane" => ["nullable"],
            "data.attributes.fecha" => ["nullable"],
            "data.attributes.per_page" => ["nullable"],
            "data.attributes.current_page" => ["nullable"],
            "data.attributes.usuario_actual" => ["nullable"]
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
