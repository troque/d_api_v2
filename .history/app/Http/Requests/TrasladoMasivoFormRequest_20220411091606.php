<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrasladoMasivoFormRequest extends FormRequest
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
            "data.attributes.id_dependencia_origen" => ["nullable"],
            "data.attributes.usuario_a_remitir" => ["nullable"],
            "data.attributes.lista_procesos" => ["nullable"],
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
