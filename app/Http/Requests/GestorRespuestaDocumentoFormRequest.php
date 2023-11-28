<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GestorRespuestaDocumentoFormRequest extends FormRequest
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
            "data.attributes.*.id_proceso_disciplinario" => ["required"],
            "data.attributes.*.aprobado" => ["required"],
            "data.attributes.*.descripcion" => ["required"],
            "data.attributes.*.version" => ["required"],
            "data.attributes.*.nuevo_documento" => ["required"],
            "data.attributes.*.id_tipo_evaluacion" => ["required"],
            "data.attributes.*.extension" => ["required_if:data.attributes.nuevo_documento,true"],
            "data.attributes.*.file64" => ["required_if:data.attributes.nuevo_documento,true"],
            "data.attributes.*.estado" => ["required_if:data.attributes.nuevo_documento,true"],
            "data.attributes.*.num_radicado" => ["required_if:data.attributes.nuevo_documento,true"],
            "data.attributes.*.vigencia" => ["required_if:data.attributes.nuevo_documento,true"],
            "data.attributes.*.num_folios" => ["required_if:data.attributes.nuevo_documento,true"],
            "data.attributes.*.peso" => ["nullable"],
            "data.attributes.*.nombre_archivo" => ["required_if:data.attributes.nuevo_documento,true"],
            "data.attributes.*.id_mas_formato" => ["required_if:data.attributes.nuevo_documento,true"],
            "data.attributes.*.grupo" => [""],
            "data.attributes.*.es_soporte" => ["required_if:data.attributes.nuevo_documento,true"],
            "data.attributes.*.es_compulsa" => ["nullable"],
            "data.attributes.*.id_log_proceso_disciplinario" => ["nullable"],
            "data.attributes.*.eliminado" => ["nullable"]
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
