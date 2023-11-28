<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogEtapaFormRequest extends FormRequest
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
            "data.attributes.id_proceso_disciplinario" => ["required"],
            "data.attributes.id_etapa" => ["required"],
            "data.attributes.id_fase" => [""],
            "data.attributes.id_tipo_cambio" => ["required"],
            "data.attributes.id_estado" => ["required"],
            "data.attributes.descripcion" => ["required"],
            "data.attributes.created_user" => ["required"],

        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
