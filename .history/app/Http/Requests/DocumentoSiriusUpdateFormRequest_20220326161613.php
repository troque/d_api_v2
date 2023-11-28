<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoSiriusUpdateFormRequest extends FormRequest
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
            "data.attributes.descripcion" => ["nullable"],
            "data.attributes.id_proceso_disciplinario" => ["required"],
            "data.attributes.update_user" => ["required"],
            "data.attributes.update_at" => ["required"],
            "data.attributes.estado" => ["nullable"],
            "data.attributes.estado_observacion" => ["nullable"],
            "data.attributes.id_etapa" => ["required"],
            "data.attributes.id_fase" => ["required"],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
