<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdenFuncionarioRequest extends FormRequest
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
            "data.attributes.*.id_funcionario" => ["required"],
            "data.attributes.*.orden" => ["required"],
            "data.attributes.*.id_evaluacion" => ["required"],
            "data.attributes.*.id_expediente" => ["nullable"],
            "data.attributes.*.id_sub_expediente" => ["nullable"],
            "data.attributes.*.id_tercer_expediente" => ["nullable"],
            "data.attributes.*.unico_rol" => ["required"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
