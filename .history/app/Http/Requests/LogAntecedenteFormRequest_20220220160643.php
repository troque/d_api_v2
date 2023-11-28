<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogAntecedenteFormRequest extends FormRequest
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
            "data.attributes.id_antecedente" => ["nullable"],
            "data.attributes.observacion_estado" => ["required"],
            "data.attributes.descripcion" => ["required"],
            "data.attributes.created_user" => [""],
            "data.attributes.created_at" => ["required"]
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
