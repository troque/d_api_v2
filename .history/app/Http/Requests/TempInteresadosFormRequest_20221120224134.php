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
            "data.attributes.radicado" => ["require"],
            "data.attributes.vigencia" => ["require"],
            "data.attributes.item" => ["require"],
            "data.attributes.tipo_interesado" => ["nullable"],
            "data.attributes.tipo_sujeto_procesal" => ["nullable"],
            "data.attributes.primer_nombre" => ["nullable"],
            "data.attributes.segundo_nombre" => ["nullable"],
            "data.attributes.primer_apellido" => ["nullable"],
            "data.attributes.segundo_apellido" => ["nullable"],
            "data.attributes.tipo_documento" => ["nullable"],
            "data.attributes.email" => ["nullable"],
            "data.attributes.telefono" => ["nullable"],
            "data.attributes.telefono2" => ["nullable"],
            "data.attributes.cargo" => ["nullable"],
            "data.attributes.orientacion_sexual" => ["nullable"],
            "data.attributes.sexo" => ["nullable"],
            "data.attributes.direccion" => ["nullable"],
            "data.attributes.departamento" => ["nullable"],
            "data.attributes.ciudad" => ["nullable"],
            "data.attributes.localidad" => ["nullable"],
            "data.attributes.entidad" => ["nullable"],
            "data.attributes.sector" => ["nullable"],

        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
