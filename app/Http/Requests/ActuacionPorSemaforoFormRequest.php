<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActuacionPorSemaforoFormRequest extends FormRequest
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
            "data.attributes.id_interesado" => ["required"],
            "data.attributes.id_actuacion" => ["required"],
            "data.attributes.fecha_inicio" => ["required"],
            "data.attributes.fecha_fin" => [""],
            "data.attributes.observaciones" => ["required"],
            "data.attributes.finalizo" => [""],
            "data.attributes.fechafinalizo" => [""],
            "data.attributes.estado" => ["required"],
            "data.attributes.created_user" => [""],
            "data.attributes.updated_user" => [""],
            "data.attributes.deleted_user" => [""],
        ];
    }
    /*
    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
    */
}
