<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\Antecedente\AntecedenteResource;
use App\Http\Resources\LogProcesoDisciplinario\LogProcesoDisciplinarioResource;
use App\Models\AntecedenteModel;
use App\Models\ClasificacionRadicadoModel;
use App\Models\LogProcesoDisciplinarioModel;
use App\Repositories\RepositoryGeneric;

trait ClasificacionRadicadoTrait
{

    /**
     *
     */
    public static function storeClasificacionRadicado($id_proceso_disciplinario)
    {
        $repository = new RepositoryGeneric();
        $repository->setModel(new ClasificacionRadicadoModel());

        $request['id_proceso_disciplinario'] = $id_proceso_disciplinario;
        $request['id_etapa'] = $id_proceso_disciplinario;
        $request['id_tipo_expediente'] = $id_proceso_disciplinario;
        $request['observaciones'] = $id_proceso_disciplinario;
        $request['id_tipo_queja'] = $id_proceso_disciplinario;
        $request['id_termino_respuesta'] = $id_proceso_disciplinario;
        $request['estado'] = $id_proceso_disciplinario;
        $request['id_dependencia'] = $id_proceso_disciplinario;



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

        $request['descripcion'] = substr($antecedenteRequest['descripcion'], 0, 4000);
        $antecedente = $repository_antecedente->create($antecedenteRequest);
        return $antecedente->uuid;
        //return AntecedenteResource::make($repository_antecedente->create($antecedenteRequest));
    }
}

