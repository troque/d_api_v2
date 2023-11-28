<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcesoDiciplinarioFormRequest extends FormRequest
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
            "data.attributes.id_origen_radicado" => [""],
            "data.attributes.id_tipo_proceso" => ["required"],
            "data.attributes.antecedente" => ["required"],
            "data.attributes.fecha_ingreso" => ["required"], //FECHA_INGRESO_DEPENDENCIA
            "data.attributes.vigencia" => [""],
            "data.attributes.id_etapa" => [""],
            "data.attributes.id_funcionario_asignado" => [""],
            "data.attributes.created_user" => [""],
            "data.attributes.id_dependencia" => [""],
            "data.attributes.id_dependencia_duena" => [""],
            "data.attributes.id_dependencia_actual" => [""],
            "data.attributes.vigencia_origen" => [""],
            //SIRIUS
            "data.attributes.radicado_entidad" => ["required_if:data.attributes.id_tipo_proceso,1"],
            //DESGLOCE
            "data.attributes.numero_auto" => ["required_if:data.attributes.id_tipo_proceso,2"],
            //"data.attributes.auto_asociado" => ["required_if:data.attributes.id_tipo_proceso,2"],
            "data.attributes.fecha_auto_desglose" => ["required_if:data.attributes.id_tipo_proceso,2"],
            "data.attributes.id_dependencia_origen" => ["required_if:data.attributes.id_tipo_proceso,2"],
            "data.attributes.id_dependencia_duena" => ["required_if:data.attributes.id_tipo_proceso,2"],
            "data.attributes.radicado_padre" => ["required_if:data.attributes.id_tipo_proceso,2"],
            "data.attributes.vigencia_padre" => ["required_if:data.attributes.id_tipo_proceso,2"],
            //PODER PREFERENTE
            "data.attributes.entidad_involucrada" => ["required_if:data.attributes.id_tipo_proceso,4"],
            "data.attributes.dependencia_cargo" => ["required_if:data.attributes.id_tipo_proceso,4"],
            "data.attributes.id_etapa_asignada" => ["required_if:data.attributes.id_tipo_proceso,4"],
            // MIGRACION
            "data.attributes.migrado" => [""],
            "data.attributes.fuente_bd" => [""],
            "data.attributes.fuente_excel" => [""],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
