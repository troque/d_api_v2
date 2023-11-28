<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoSiriusFormRequest extends FormRequest
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
            "data.attributes.*.id_etapa" => ["required"],
            "data.attributes.*.id_fase" => ["required"],
            "data.attributes.*.file64" => ["required_without:data.attributes.*.es_compulsa"],
            "data.attributes.*.estado" => ["required"],
            "data.attributes.*.num_radicado" => ["required"],
            "data.attributes.*.vigencia" => ["required"],
            "data.attributes.*.descripcion" => ["required"],
            "data.attributes.*.peso" => ["nullable"],
            "data.attributes.*.nombre_archivo" => ["required"],

            "data.attributes.*.grupo" => [""],
            "data.attributes.*.es_soporte" => ["required"],
            "data.attributes.*.es_compulsa" => ["nullable"],
            "data.attributes.*.id_log_proceso_disciplinario" => ["nullable"],

            "data.attributes.*.extension" => ["required_if:data.attributes.*.es_compulsa,false"],
            "data.attributes.*.num_folios" => ["required_if:data.attributes.*.es_compulsa,false"],
            "data.attributes.*.id_mas_formato" => ["required_if:data.attributes.*.es_compulsa,false"],

            //COMPULSA
            /*/"data.attributes.*.id_proceso_disciplinario_compulsa" => ["required_if:data.attributes.*.es_compulsa,true"],
            "data.attributes.*.radicado_compulsa" => ["required_if:data.attributes.*.es_compulsa,true"],
            "data.attributes.*.vigencia_compulsa" => ["required_if:data.attributes.*.es_compulsa,true"],
            "data.attributes.*.nombre_archivo" => ["required_if:data.attributes.*.es_compulsa,true"],
            "data.attributes.*.path" => ["required_if:data.attributes.*.es_compulsa,true"],
            "data.attributes.*.id_documento_sirius_compulsa" => ["required_if:data.attributes.*.es_compulsa,true"],*/
            "data.attributes.*.sirius_track_id" => ["required_if:data.attributes.*.es_compulsa,true"],
            "data.attributes.*.sirius_ecm_id" => ["required_if:data.attributes.*.es_compulsa,true"],
            "data.attributes.*.documento_vacio" => ["required_if:data.attributes.*.es_compulsa,true"],
            "data.attributes.*.seguimiento" => ["nullable"],
            "data.attributes.*.descripcion_seguimiento" => ["nullable"],
            "data.attributes.*.eliminado" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
