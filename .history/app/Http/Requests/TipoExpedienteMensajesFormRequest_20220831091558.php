<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoExpedienteMensajesFormRequest extends FormRequest
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
            "data.attributes.mensaje" => ["required"],
            "data.attributes.id_tipo_expediente" => ["required"],
            "data.attributes.id_sub_tipo_expediente" => ["nullable"],
            "data.attributes.estado" => ["required"]
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}