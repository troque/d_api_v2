<?php

namespace App\Http\Requests;

use Adldap\Configuration\Validators\Validator;
use Illuminate\Foundation\Http\FormRequest;

class LoginFormRequest extends FormRequest
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
            "data.attributes.user" => ["required"],
            "data.attributes.password" => ["required"],
        ];
    }

    public function validated(): Validator
    {
        return parent::validated()["data"]["attributes"];
    }
}
