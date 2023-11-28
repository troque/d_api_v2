<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoConductaProcesoDisciplinarioFormRequest extends FormRequest
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
            "data.attributes.conducta_nombre" => [""],
            "data.attributes.estado" => [""],
            "data.attributes.id_proceso_disciplinario" => [""],

        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
