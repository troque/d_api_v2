<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcesoDisciplinarioPorSemaforoFormRequest extends FormRequest
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
            "data.attributes.id_semaforo" => ["required"],
            "data.attributes.id_proceso_disciplinario" => ["required"],
            "data.attributes.id_actuacion" => ["nullable"],
            "data.attributes.fecha_inicio" => ["required"],
            "data.attributes.estado" => ["required"],
            "data.attributes.created_user" => [""],
            "data.attributes.updated_user" => [""],
            "data.attributes.deleted_user" => [""],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
