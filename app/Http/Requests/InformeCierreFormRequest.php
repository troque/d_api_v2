<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InformeCierreFormRequest extends FormRequest
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
            "data.attributes.radicado_sirius" => ["required"],
            "data.attributes.documento_sirius" => ["required"],
            "data.attributes.descripcion" => ["required"],
            "data.attributes.nombre_archivo" => ["required"],
            "data.attributes.numero_folios" => ["required"],
            "data.attributes.radicado" => ["required"],
            "data.attributes.id_etapa" => ["required"],
            "data.attributes.id_fase" => ["required"],
            "data.attributes.eliminado" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
