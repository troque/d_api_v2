<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuscadorFormRequest extends FormRequest
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
            "data.attributes.radicado" => ["nullable"],
            "data.attributes.vigencia" => ["nullable"],
            "data.attributes.estado_expediente" => ["nullable"],
            "data.attributes.dependencia" => ["nullable"],
            "data.attributes.etapa" => ["nullable"],
            "data.attributes.antecedente" => ["nullable"],
            "data.attributes.nombre_investigado" => ["nullable"],
            "data.attributes.cargo_investigado" => ["nullable"],
            "data.attributes.tipo_quejoso" => ["nullable"],
            "data.attributes.primer_nombre_quejoso" => ["nullable"],
            "data.attributes.segundo_nombre_quejoso" => ["nullable"],
            "data.attributes.primer_apellido_quejoso" => ["nullable"],
            "data.attributes.segundo_apellido_quejoso" => ["nullable"],
            "data.attributes.numero_documento" => ["nullable"],
            "data.attributes.sujeto_procesal" => ["nullable"],
            "data.attributes.funcionario_actual" => ["nullable"],
            "data.attributes.evaluacion" => ["nullable"],
            "data.attributes.tipo_conducta" => ["nullable"],
            "data.attributes.auto" => ["nullable"],
            "data.attributes.entidad" => ["nullable"],
            "data.attributes.sector" => ["nullable"],
            "data.attributes.per_page" => ["nullable"],
            "data.attributes.current_page" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
