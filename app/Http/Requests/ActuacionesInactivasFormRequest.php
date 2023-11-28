<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActuacionesInactivasFormRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "data.attributes.*.id_actuacion" => ["nullable"],
            "data.attributes.*.id_actuacion_principal" => ["nullable"],
            "data.attributes.*.id_proceso_disciplinario" => ["nullable"],
            "data.attributes.*.estado_inactivo" => ["nullable"]
        ];
    }
}
