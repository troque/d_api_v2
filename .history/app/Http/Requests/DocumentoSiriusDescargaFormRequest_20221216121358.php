<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoSiriusDescargaFormRequest extends FormRequest
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
            "data.attributes.id_documento_sirius" => ["required"],
            "data.attributes.es_compulsa" => ["required"],
            // "data.attributes.extension" => ["required_if:data.attributes.es_compulsa,false"],
            // "data.attributes.radicado" => ["required_if:data.attributes.es_compulsa,false"],
            // "data.attributes.vigencia" => ["required_if:data.attributes.es_compulsa,false"],
            "data.attributes.extension" => ["nullable"],
            "data.attributes.radicado" => ["nullable"],
            "data.attributes.vigencia" => ["nullable"],
            "data.attributes.consulta_sirius" => ["nullable"],
            "data.attributes.versionLabel" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
