<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogProcesoDisciplinarioFormRequest extends FormRequest
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
            "data.attributes.id_proceso_disciplinario" => ["required"],
            "data.attributes.id_etapa" => [""],
            "data.attributes.id_fase" => [""],
            "data.attributes.id_dependencia_origen" => [""],
            "data.attributes.id_tipo_log" => [""],
            "data.attributes.id_estado" => [""],
            "data.attributes.descripcion" => [""],
            "data.attributes.created_user" => [""],
            "data.attributes.documentos" => [""],
            "data.attributes.id_funcionario_actual" => [""],
            "data.attributes.id_funcionario_registra" => [""],
            "data.attributes.id_tipo_transaccion" => [""],
            "data.attributes.id_funcionario_asignado" => [""],
            "data.attributes.created_at" => [""],

        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
