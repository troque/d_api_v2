<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MasConsecutivoActuacionesFormRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "data.attributes.id_vigencia" => ["required"],
            "data.attributes.consecutivo" => ["nullable"],
            "data.attributes.estado" => ["nullable"],
            "data.attributes.form" => ["nullable"],
        ];
    }
}
