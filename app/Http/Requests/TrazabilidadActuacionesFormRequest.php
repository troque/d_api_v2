<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrazabilidadActuacionesFormRequest extends FormRequest
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
            "data.attributes.uuid_actuacion" => ["nullable"],
            "data.attributes.id_estado_actuacion" => ["nullable"],
            "data.attributes.observacion" => ["nullable"],
            "data.attributes.estado" => ["nullable"],
            "data.attributes.envia_correo" => ["nullable"],
            "data.attributes.id_proceso_disciplinario" => ["nullable"],
            "data.attributes.activacion" => ["nullable"],
            "data.attributes.id_mas_actuacion" => ["nullable"],
            "data.attributes.id_etapa" => ["nullable"],
            "data.attributes.validar_semaforos" => ["nullable"],
            "data.attributes.rechazo" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
