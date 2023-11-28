<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TempEntidadesFormRequest extends FormRequest
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
            "data.attributes.radicado" => ["required"],
            "data.attributes.vigencia" => ["required"],
            "data.attributes.item" => ["required"],
            "data.attributes.id_entidad" => ["nullable"],
            "data.attributes.direccion" => ["nullable"],
            "data.attributes.sector" => ["nullable"],
            "data.attributes.nombre_investigado" => ["nullable"],
            "data.attributes.cargo_investigado" => ["nullable"],
            "data.attributes.observaciones" => ["nullable"],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
