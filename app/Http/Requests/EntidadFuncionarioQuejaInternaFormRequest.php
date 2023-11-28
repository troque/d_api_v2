<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntidadFuncionarioQuejaInternaFormRequest extends FormRequest
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
            "data.attributes.se_identifica_investigado" => ["required"],
            "data.attributes.id_proceso_disciplinario" => [""],
            "data.attributes.id_tipo_funcionario" => [""],
            "data.attributes.id_tipo_documento" => [""],
            "data.attributes.numero_documento" => [""],
            "data.attributes.primer_nombre" => [""],
            "data.attributes.segundo_nombre" => [""],
            "data.attributes.primer_apellido" => [""],
            "data.attributes.segundo_apellido" => [""],
            "data.attributes.razon_social" => [""],
            "data.attributes.numero_contrato" => [""],
            "data.attributes.dependencia" => [""],
            "data.attributes.observaciones" => [""],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
