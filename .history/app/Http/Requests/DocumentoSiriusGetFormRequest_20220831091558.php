<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoSiriusGetFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    public function rules()
    {
        return [
            "data.attributes.solo_sirius" => ["nulleable"],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
