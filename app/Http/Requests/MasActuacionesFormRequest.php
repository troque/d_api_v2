<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MasActuacionesFormRequest extends FormRequest
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
            "data.attributes.nombre_actuacion" => ["required"],
            "data.attributes.nombre_plantilla" => ["required"],
            //"data.attributes.id_etapa" => ["required"],
            "data.attributes.estado" => ["required"],
            //"data.attributes.id_etapa_despues_aprobacion" => ["nullable"],
            "data.attributes.etapa_siguiente" => ["nullable"],
            "data.attributes.despues_aprobacion_listar_actuacion" => ["nullable"],
            "data.attributes.generar_auto" => ["nullable"],
            "data.attributes.nombre_plantilla_manual" => ["nullable"],
            "data.attributes.fileBase64" => ["required"],
            "data.attributes.fileBase64_manual" => ["nullable"],
            "data.attributes.texto_dejar_en_mis_pendientes" => ["nullable"],
            "data.attributes.texto_enviar_a_alguien_de_mi_dependencia" => ["nullable"],
            "data.attributes.texto_enviar_a_jefe_de_la_dependencia" => ["nullable"],
            "data.attributes.texto_enviar_a_otra_dependencia" => ["nullable"],
            "data.attributes.texto_regresar_proceso_al_ultimo_usuario" => ["nullable"],
            "data.attributes.texto_enviar_a_alguien_de_secretaria_comun_dirigido" => ["nullable"],
            "data.attributes.texto_enviar_a_alguien_de_secretaria_comun_aleatorio" => ["nullable"],
            "data.attributes.tipo_actuacion" => ["required"],
            "data.attributes.excluyente" => ["nullable"],
            "data.attributes.cierra_proceso" => ["nullable"],
            "data.attributes.visible" => ["nullable"],
            "data.attributes.campos" => ["nullable"],
            "data.attributes.etapa_siguiente" => ["nullable"],
            "data.attributes.lista_roles" => ["nullable"],
            "data.attributes.lista_etapa" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
