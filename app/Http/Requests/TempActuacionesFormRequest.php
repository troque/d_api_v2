<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TempActuacionesFormRequest extends FormRequest
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
            "data.attributes.radicado" => ["required"],
            "data.attributes.vigencia" => ["required"],
            "data.attributes.item" => ["required"],
            "data.attributes.nombre" => ["nullable"],
            "data.attributes.tipo" => ["nullable"],
            "data.attributes.autonumero" => ["nullable"],
            "data.attributes.fecha" => ["nullable"],
            "data.attributes.path" => ["nullable"],
            "data.attributes.dependencia" => ["nullable"],
            "data.attributes.documentoData" => ["nullable"],
            "data.attributes.documentoBase64" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
