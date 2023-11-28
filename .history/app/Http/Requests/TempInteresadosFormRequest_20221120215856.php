<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TempInteresadosFormRequest extends FormRequest
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
            "data.attributes.tipo_interesado" => [""],
            "data.attributes.tipo_sujeto_procesal" => [""],
            "data.attributes.primer_nombre" => [""],
            "data.attributes.segundo_nombre" => [""],
            "data.attributes.primer_apellido" => [""],
            "data.attributes.segundo_apellido" => [""],
            "data.attributes.tipo_documento" => [""],
            "data.attributes.email" => [""],
            "data.attributes.telefono" => [""],
            "data.attributes.telefono2" => [""],
            "data.attributes.cargo" => [""],
            "data.attributes.orientacion_sexual" => [""],
            "data.attributes.sexo" => [""],
            "data.attributes.direccion" => [""],
            "data.attributes.departamento" => [""],
            "data.attributes.ciudad" => [""],
            "data.attributes.localidad" => [""],
            "data.attributes.entidad" => [""],
            "data.attributes.sector" => [""],
            "data.attributes.radicado" => ["require"],
            "data.attributes.vigencia" => ["require"],
            "data.attributes.item" => ["require"],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
