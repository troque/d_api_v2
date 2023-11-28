<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TempActuacionesFormRequest extends FormRequest
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
        /*return [
            "data.attributes.radicado" => ["required"],
            "data.attributes.vigencia" => ["required"],
            "data.attributes.item" => ["required"],
            "data.attributes.nombre" => ["nullable"],
            "data.attributes.tipo" => ["nullable"],
            "data.attributes.autonumero" => ["nullable"],
            "data.attributes.fecha" => ["nullable"],
            "data.attributes.path" => ["nullable"],
            "data.attributes.documentoData" => ["nullable"],
            "data.attributes.documentoBase64" => ["nullable"],
        ];*/

        return [
            "data.attributes.radicado" => ["required"],
            "data.attributes.vigencia" => ["required"],
            "data.attributes.item" => ["required"],
            "data.attributes.autonumero" => ["nullable"],
            "data.attributes.fecha" => ["nullable"],
            "data.attributes.path" => ["nullable"],
            "data.attributes.documentoBase64" => ["nullable"],
            /*"data.attributes.fecha" => ["nullable"],
            "data.attributes.nombre" => ["nullable"],
            "data.attributes.tipo" => ["nullable"],
            "data.attributes.autonumero" => ["nullable"],
            "data.attributes.fecha" => ["nullable"],
            "data.attributes.fechatermino" => ["nullable"],
            "data.attributes.instancia" => ["nullable"],
            "data.attributes.decision" => ["nullable"],
            "data.attributes.observacion" => ["nullable"],
            "data.attributes.terminomonto" => ["nullable"],*/
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
