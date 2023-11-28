<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HasAccessFormRequest extends FormRequest
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
            "data.attributes.modulo" => ["required"],
            "data.attributes.funcionalidad" => ["required"],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
