<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemisionQuejaFormRequest extends FormRequest
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
            //Considerar los campos de acuerdo a la accion
            //Variables obligatorias
            "data.attributes.consulta" => ["required"],
            "data.attributes.id_proceso_disciplinario" => ["required"],
            "data.attributes.id_tipo_evaluacion" => ["required"],
            //Variables para consulta de expediente
            "data.attributes.id_tipo_dependencia_acceso" => ["required_if:data.attributes.consulta,'false'"],
            "data.attributes.id_dependencia_destino" => ["required_if:data.attributes.consulta,'false'"],
            "data.attributes.expediente" => ["required_if:data.attributes.consulta,'false'"],
            "data.attributes.vigencia" => ["required_if:data.attributes.consulta,'false'"],
            "data.attributes.version" => ["required_if:data.attributes.consulta,'false'"],
            //Variables para guardar
            //INCORPORACION
            "data.attributes.id_proceso_disciplinario_expediente" => ["required_if:data.attributes.consulta,'false'"],
            "data.attributes.eliminado" => ["nullable"]
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
