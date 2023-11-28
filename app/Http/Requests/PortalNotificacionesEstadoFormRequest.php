<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortalNotificacionesEstadoFormRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "data.attributes.id_notificacion" => ["required"],
            "data.attributes.descripcion" => ["required"],
            "data.attributes.estado" => ["required"],
        ];
    }
}
