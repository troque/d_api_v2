<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcesoSinprocFormRequest extends FormRequest
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
            //"data.attributes.id_origen_radicado" => ["required"],
            "data.attributes.id_tipo_proceso" => ["required"],
            "data.attributes.vigencia" => ["required"],
            "data.attributes.vigencia_origen" => [""],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
