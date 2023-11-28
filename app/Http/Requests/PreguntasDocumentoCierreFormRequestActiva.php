<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreguntasDocumentoCierreFormRequestActiva extends FormRequest
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
            "data.attributes.requiere_documento" => ["required"],
            "data.attributes.compulsa_copias" => ["required"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
