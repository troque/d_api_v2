<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClasificacionRadicadoFormRequest extends FormRequest
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
            "data.attributes.uuid" => [""],
            "data.attributes.id_proceso_disciplinario" => ["required"],
            "data.attributes.id_etapa" => ["required"],
            "data.attributes.id_tipo_expediente" => ["nullable"],
            "data.attributes.observaciones" => ["nullable"],
            "data.attributes.id_tipo_queja" => ["nullable"],
            "data.attributes.id_termino_respuesta" => ["nullable"],
            "data.attributes.fecha_termino" => ["nullable"],
            "data.attributes.hora_termino" => ["nullable"],
            "data.attributes.gestion_juridica" => ["nullable"],
            "data.attributes.estado" => ["nullable"],
            "data.attributes.id_estado_reparto" => ["nullable"],
            "data.attributes.oficina_control_interno" => ["nullable"],
            "data.attributes.id_tipo_derecho_peticion" => ["nullable"],
            "data.attributes.created_user" => [""],
            "data.attributes.per_page" => ["nullable"],
            "data.attributes.current_page" => ["nullable"],
            "data.attributes.reclasificacion" => ["nullable"],
            "data.attributes.reparto" => ["nullable"],
            "data.attributes.id_dependencia" => ["nullable"],
            "data.attributes.validacion_jefe" => ["nullable"],
            "data.attributes.id_fase" => ["nullable"],
            "data.attributes.id_tipo_transaccion" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
