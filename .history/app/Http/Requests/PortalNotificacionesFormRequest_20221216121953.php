<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortalNotificacionesFormRequest extends FormRequest
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
            "data.attributes.uuid_proceso_disciplinario" => ["nullable"],
            "data.attributes.numero_documento" => ["required"],
            "data.attributes.tipo_documento" => ["required"],
            "data.attributes.detalle" => ["required"],
            "data.attributes.radicado" => ["required"],
            "data.attributes.estado" => ["required"],
            "data.attributes.numero_radicado_sirius" => ["required"],
            "data.attributes.correo" => ["nullable"],
            "data.attributes.nombreCompleto" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
