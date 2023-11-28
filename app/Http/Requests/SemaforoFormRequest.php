<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SemaforoFormRequest extends FormRequest
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
            "data.attributes.nombre" => ["required"],
            "data.attributes.id_mas_evento_inicio" => ["required"],
            "data.attributes.id_etapa" => [""],
            "data.attributes.id_mas_actuacion_inicia" => [""],
            "data.attributes.id_mas_dependencia_inicia" => [""],
            "data.attributes.id_mas_grupo_trabajo_inicia" => [""],
            "data.attributes.nombre_campo_fecha" => [""],
            "data.attributes.estado" => ["required"],
            "data.attributes.created_user" => [""],
            "data.attributes.updated_user" => [""],
            "data.attributes.deleted_user" => [""],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
