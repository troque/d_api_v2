<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortalLogFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // return [
        //     "data.attributes.id_actuacion" => ["required"],
        //     "data.attributes.usuario_accion" => ["required"],
        //     "data.attributes.id_estado_actuacion" => ["required"],
        //     "data.attributes.documento_ruta" => ["nullable"],
        //     "data.attributes.estado" => ["required"],
        //     "data.attributes.fileBase64" => ["nullable"],
        //     "data.attributes.nombre_archivo" => ["nullable"],
        //     "data.attributes.ext" => ["nullable"],
        //     "data.attributes.peso" => ["nullable"],
        //     "data.attributes.id_proceso_disciplinario" => ["required"],
        //     "data.attributes.id_etapa" => ["required"],
        //     "data.attributes.data" => ["nullable"]
        // ];
    }
}