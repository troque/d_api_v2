<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParametrizacionActuacionesFormRequest extends FormRequest
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
            "data.attributes.nombre_actuacion" => ["nullable"],
            "data.attributes.nombre_plantilla" => ["nullable"],
            "data.attributes.id_etapa" => ["nullable"],
            "data.attributes.estado" => ["nullable"],
            "data.attributes.id_etapa_despues_aprobacion" => ["nullable"],
            "data.attributes.despues_aprobacion_listar_actuacion" => ["nullable"],
            "data.attributes.created_user" => ["nullable"],
            "data.attributes.updated_user" => ["nullable"],
            "data.attributes.deleted_user" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
