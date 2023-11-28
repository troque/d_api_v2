<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatosInteresadoFormRequest extends FormRequest
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
            "data.attributes.id_tipo_interesao" => ["nullable"],
            "data.attributes.id_tipo_sujeto_procesal" => ["nullable"],
            "data.attributes.tipo_documento" => ["nullable"],
            "data.attributes.numero_documento" => ["nullable"],
            "data.attributes.primer_nombre" => ["nullable"],
            "data.attributes.segundo_nombre" => ["nullable"],
            "data.attributes.primer_apellido" => ["nullable"],
            "data.attributes.segundo_apellido" => ["nullable"],
            "data.attributes.id_departamento" => ["nullable"],
            "data.attributes.id_ciudad" => ["nullable"],
            "data.attributes.direccion" => ["nullable"],
            "data.attributes.direccion_json" => ["nullable"],
            "data.attributes.id_localidad" => ["nullable"],
            "data.attributes.email" => ["nullable"],
            "data.attributes.telefono_celular" => ["nullable"],
            "data.attributes.telefono_fijo" => ["nullable"],
            "data.attributes.id_sexo" => ["nullable"],
            "data.attributes.id_genero" => ["nullable"],
            "data.attributes.id_orientacion_sexual" => ["nullable"],
            "data.attributes.cargo" => ["nullable"],
            "data.attributes.cargo_descripcion" => ["nullable"],
            "data.attributes.tarjeta_profesional" => ["nullable"],
            "data.attributes.id_tipo_entidad" => ["nullable"],
            "data.attributes.nombre_entidad" => ["nullable"],
            "data.attributes.entidad" => ["nullable"],
            "data.attributes.id_entidad" => ["nullable"],
            "data.attributes.id_dependencia" => ["nullable"],
            "data.attributes.id_dependencia_entidad" => ["nullable"],
            "data.attributes.id_proceso_disciplinario" => ["nullable"],
            "data.attributes.folio" => ["nullable"],
            "data.attributes.estado" => ["nullable"],
            "data.attributes.estado_observacion" => ["nullable"],
            "data.attributes.per_page" => ["nullable"],
            "data.attributes.current_page" => ["nullable"],
            "data.attributes.autorizar_envio_correo" => ["nullable"],
        ];
    }

    /*public function validated()
    {
        return parent::validated()["data"]["attributes"];
    }*/
}
