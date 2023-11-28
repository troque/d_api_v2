<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvaluacionFaseListaFormRequest extends FormRequest
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
            "data.attributes.*.id_resultado_evaluacion" => ["nullable"],
            "data.attributes.*.id_tipo_expediente" => ["nullable"],
            "data.attributes.*.id_sub_tipo_expediente" => ["nullable"],
            "data.attributes.*.id_tercer_expediente" => ["nullable"],
            "data.attributes.*.id_fase_actual" => ["nullable"],
            "data.attributes.*.id_fase_antecesora" => ["nullable"],
            "data.attributes.*.orden" => ["nullable"]
        ];
    }
}
