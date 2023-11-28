<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogAntecedenteFormRequest extends FormRequest
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
            "data.attributes.id_dependencia" => ["nullable"],
            "data.attributes.descripcion" => ["required"],
            "data.attributes.id_proceso_disciplinario" => ["required"],
            "data.attributes.created_user" => [""],
            "data.attributes.estado" => [""]


            // $table->string('id_antecedente');
            // $table->string('observacion_estado');
            // $table->string('descripcion')->nullable();
            // $table->string("created_user", 256)->nullable();

            /*"data.attributes.descripcion" => ["required_if:data.attributes.antecedentes,3"],
            "data.attributes.id_proceso_disciplinario" => ["required_if:data.attributes.antecedentes,3"],*/
        ];
    }

    public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }
}
