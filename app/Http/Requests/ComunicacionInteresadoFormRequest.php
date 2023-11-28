<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComunicacionInteresadoFormRequest extends FormRequest
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
            "data.attributes.concatenado" => ["nullable"],
            "data.attributes.id_proceso_disciplinario" => ["required"],
            "data.attributes.eliminado" => ["nullable"]
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
