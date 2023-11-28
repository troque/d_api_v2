<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CambioEtapaFormRequest extends FormRequest
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
            "data.attributes.uuid_proceso_disciplinario" => ["required"],
            "data.attributes.id_etapa" => ["required"],
            "data.attributes.justificacion" => ["required"],
        ];
    }
}
