<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioFormRequest extends FormRequest
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
            "data.attributes.nombre" => ["nullable"],
            "data.attributes.apellido" => ["nullable"],
            "data.attributes.name" => ["nullable"],
            "data.attributes.identificacion" => ["nullable"],
            "data.attributes.email" => ["nullable"],
            "data.attributes.estado" => ["nullable"],
            "data.attributes.id_dependencia" => ["nullable"],
            "data.attributes.roles" => ["nullable"],
            "data.attributes.expedientes" => ["nullable"],
            "data.attributes.reparto_habilitado" => ["nullable"],

            //SOLO PARA CONSULTAR LAS FUNCIONALIDADES
            "data.attributes.nombre_funcionalidad" => ["nullable"],
            "data.attributes.nombre_modulo" => ["nullable"],

            //SOlO PARA ACTUALIZAR LA FIRMA MECANICA
            "data.attributes.firma_mecanica" => ["nullable"],
            "data.attributes.password_firma_mecanica" => ["nullable"],
            "data.attributes.firma_mecanica_fileBase64" => ["nullable"],

            // SI EL USUARIO PERTENECE A LA DEPENDENCIA DE SCRETARIA COMUN
            // PODRA GUARDAR A QUE GRUPO DE TRABAJO PERTENECE
            "data.attributes.id_mas_grupo_trabajo_secretaria_comun" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
