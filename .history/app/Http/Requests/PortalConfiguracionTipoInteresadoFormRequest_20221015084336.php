<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortalConfiguracionTipoInteresadoFormRequest extends FormRequest
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
            "data.attributes.id_tipo_sujeto_procesal" => ["required"],
            "data.attributes.permiso_consulta" => ["required"],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}