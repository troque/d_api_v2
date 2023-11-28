<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SemeforoProcesoDisciplinarioFormRequest extends FormRequest
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
            "data.attributes.id_proceso_disciplinario" => ["required"],
            "data.attributes.id_semaforo" => ["required"],
            "data.attributes.id_actuacion_finaliza" => ["nullable"],
            "data.attributes.id_dependencia_finaliza" => ["nullable"],
            "data.attributes.id_usuario_finaliza" => ["nullable"],
        ];
    }
}
