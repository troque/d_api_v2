<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArchivoActuacionesFormRequest extends FormRequest
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
            "data.attributes.uuid_actuacion" => ["required"],
            "data.attributes.id_tipo_archivo" => ["required"],
            "data.attributes.documento_ruta" => ["nullable"],
            "data.attributes.nombre_archivo" => ["required"],
            "data.attributes.extension" => ["required"],
            "data.attributes.peso" => ["required"],
            "data.attributes.fileBase64" => ["nullable"],
            "data.attributes.tipoDocumentoActualizar" => ["nullable"]
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}