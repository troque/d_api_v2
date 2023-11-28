<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TempProcesoDisciplinarioFormRequest extends FormRequest
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
            "data.attributes.estado" => ["required"],
            "data.attributes.id_tipo_proceso" => ["required"],
            "data.attributes.id_dependencia_origen" => ["required"],
            "data.attributes.id_dependencia_duena" => ["nullable"],
            "data.attributes.id_etapa" => ["required"],
            "data.attributes.fecha_registro" => ["required"],
            "data.attributes.id_tipo_expediente" => ["nullable"],
            "data.attributes.id_sub_tipo_expediente" => ["nullable"],
            "data.attributes.id_tipo_evaluacion" => ["nullable"],
            "data.attributes.id_tipo_conducta" => ["nullable"],
            "data.attributes.radicado_padre_desglose" => ["nullable"],
            "data.attributes.vigencia_padre_desglose" => ["nullable"],
            "data.attributes.auto_desglose" => ["nullable"],
            "data.attributes.usuario_actual" => ["required"],

        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
