<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvaluacionFormRequest extends FormRequest
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
            "data.attributes.id_proceso_disciplinario" => ["nullable"],
            "data.attributes.noticia_priorizada" => ["nullable"],
            "data.attributes.justificacion" => ["nullable"],
            "data.attributes.estado" => ["nullable"],
            "data.attributes.resultado_evaluacion" => ["nullable"],
            "data.attributes.tipo_conducta" => ["nullable"],
            "data.attributes.estado_evaluacion" => ["nullable"]
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
