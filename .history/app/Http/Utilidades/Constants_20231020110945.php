<?php

namespace App\Http\Utilidades;

/**
 * Definición de constantes para todo el proyecto.
 *
 * @autor: Sandra Saavedra
 * @Fecha: 1 abril 2022
 */
abstract class Constants
{

    const TIPO_EXPEDIENTE = [
        'derecho_peticion' => 1,
        'poder_referente' => 2,
        'queja' => 3,
        'tutela' => 4,
        'proceso_disciplinario' => 5
    ];

    const ESTADOS = [
        'activo' => 1,
        'inactivo' => 0
    ];

    const TIPO_DERECHO_PETICION = [
        'copias' => 1,
        'general' => 2,
        'alerta_control_politico' => 3
    ];

    const TIPO_QUEJA = [
        'externa' => 1,
        'interna' => 2
    ];

    const TIPO_TUTELA = [
        'dias' => 1,
        'horas' => 2
    ];

    const TIPO_LOG = [
        'etapa' => 1,
        'fase' => 2
    ];

    const ESTADO_LOG_PROCESO_DISCIPLINARIO = [
        'contestado' => 1,
        'finalizado' => 2,
        'remitido' => 3,
    ];

    const TIPO_DE_PROCESO = [
        'correspondencia_sirius' => 1,
        'desglose' => 2,
        'sinproc' => 3,
        'poder_preferente' => 4,
    ];


    const ETAPA = [
        'captura_reparto' => 1,
        'evaluacion' => 2,
        'evaluacion_pd' => 3,
        'investigacion_preliminar' => 4,
        'investigacion_disciplinaria' => 5,
        'causa_juzgamiento' => 6,
        'proceso_verbal' => 7,
        'segunda_instancia' => 8,
    ];

    /**
     *
     */
    const FASE = [
        'lista_para_cierre_captura_reparto' => -1,
        'lista_para_cierre_evaluacion' => -2,
        'ninguna' => 0, // CAPTURA Y REPARTO
        'antecedentes' => 1, // CAPTURA Y REPARTO
        'datos_interesado' => 2, // CAPTURA Y REPARTO
        'clasificacion_radicado' => 3, // CAPTURA Y REPARTO
        'entidad_investigado' => 4, // CAPTURA Y REPARTO
        'soporte_radicado' => 5, // CAPTURA Y REPARTO
        'remision_queja' => 6,  // REMISION QUEJA
        'inicio_proceso_disciplinario' => 7, // PENDIENTE POR UBICACION
        'documento_cierre' => 8, // EVALUACION
        'gestor_respuesta' => 9, // EVALUACION
        'validacion_clasificacion' => 10, // EVALUACION
        'evaluacion' => 11, // EVALUACION
        'comunicacion_interesado' => 12, // EVALUACION
        'cierre_total' => 13, // EVALUACION
        'cierre_captura_reparto' => 14, // CAPTURA Y REPARTO
        'cierre_evaluacion' => 15, // EVALUACION
        'actuaciones_evaluacion_pd' => 16, // ACTUACIONES EN EVALUACION PD
        'requerimiento_juzgado' => 17, // EVALUACION
        'informe_cierre' => 18, // EVALUACION
        'registro_seguimiento' => 19, // EVALUACION
        'iniciar_proceso' => 20, // ACTUACIONES EN EVALUACION PD
        'cierre_proceso' => 21, // TODAS LAS ETAPAS EN ACTUACIONES
        'transacciones' => 22, // TODAS LAS ETAPAS EN ACTUACIONES
        'impedimento' => 23, // TODAS LAS ETAPAS EN ACTUACIONES
        'comisorio' => 24, // TODAS LAS ETAPAS EN ACTUACIONES
    ];



    const TIPO_DE_TRANSACCION = [
        'cierre_etapa' => 1,
        'anexo_documentos' => 2,
        'clasificacion_expediente' => 3,
        'reclasificacion_expediente' => 4,
        'reasignacion' => 5,
        'inicio_proceso_disciplinario' => 6,
        'ninguno' => 7,
        'inicio_de_etapa' => 8,
        'activar' => 9,
        'inactivar' => 10,
    ];

    const INTENTOS = [
        'num_reclasificaciones' => 2,
    ];

    const FUNCIONALIDAD_ROL = [
        'jefe' => 'jefe'
    ];


    const ESTADO_EVALUACION = [
        'registrado' => 1,
        'aprobado_por_jefe' => 2,
        'rechazado_por_jefe' => 3,
    ];


    const RESULTADO_EVALUACION = [
        'comisorio_eje' => 1,
        'devolucion_entidad' => 2,
        'incorporacion' => 3,
        'remisorio_externo' => 4,
        'remisorio_interno' => 5,
        'sin_evaluacion' => 6,
    ];


    const SEMAFORIZACION = [
        'red' => 1,
        'orange' => 2,
        'green' => 3,
    ];


    const TIPO_CIERRE_ETAPA = [
        'reparto_aleatorio' => 1,
        'asignado_asi_mismo' => 2,
        'asignacion_dirigida' => 3,
        'cierre_definitivo' => 4,
    ];

    const TIPO_FIRMA_MECANICA = [
        'principal' => 1,
        'firmo' => 2,
        'elaboro' => 3,
    ];

    const ESTADO_FIRMA_MECANICA = [
        'pendiente_de_firma' => 1,
        'firmado' => 2,
        'Eliminado' => 3,
    ];

    // Nombre variable carpeta actuaciones
    const ACTUACIONES_NOMBRE_CARPETA = "actuaciones";

    const ESTADOS_ACTUACION = [
        'aprobada' => 1,
        'rechazada' => 2,
        'pendiente_aprobación' => 3,
        'solicitud_inactivación' => 4,
        'aprobada_pdf_definitivo' => 5,
        'actualización_Documento' => 6,
        'solicitud_inactivación_aceptada' => 7,
        'solicitud_inactivación_rechazada' => 8,
        'documento_firmado' => 9,
        'actuacion_inactivada' => 10,
        'cambio_etapa' => 11,
        'cambio_lista_actuaciones_inactivar' => 12,
        'remitida' => 0,
        'solicitud_activacion' => 13,
        'solicitud_activacion_aceptada' => 14,
        'solicitud_activacion_rechazada' => 15,
        'cambio_fecha_registro' => 16
    ];

    const ESTADOS_VISIBILIDAD = [
        "visible_todos" => 1,
        "visible_dependencia" => 2,
        "visible_para_mi_y_jefe" => 3,
        "oculto_todos" => 4
    ];

    const TIPO_ACTUACION = [
        'actuacion' => 0,
        'impedimento' => 1,
        'comisorio' => 2,
    ];

    const TIPO_INTERESADO = [
        'persona_natural' => 1,
        'entidad' => 2,
    ];

    const TIPO_DOCUMENTO = [
        'cedula_ciudadania' => 1,
        'cedula_extranjeria' => 2,
        'pasaporte' => 3,
        'no_informa' => 4,
    ];

    const ESTADO_PROCESO_DISCIPLINARIO = [
        'activo' => 1,
        'cerrado' => 2,
        'archivado' => 3,
    ];

    const ESTADO_ACTUACION = [
        'aprobada' => 'APR',
        'rechazada' => 'RECH',
        'pendiente_aprobacion' => 'PENAPR',
        'solicitud_inactivacion' => 'SOLANUL',
        'aprobada_pdf_definitivo' => 'PDFDEF',
        'actualizacion_documento' => 'ACTDOC',
        'solicitud_inactivacion_aceptada' => 'ACTSOLANUL',
        'solicitud_inactivacion_rechazada' => 'RECHSOLANUL',
        'documento_firmado' => 'DOCFIR',
        'actuación_inactivada' => 'ACTINACT',
        'cambio_etapa' => 'CAMETP',
        'cambio_lista_actuaciones_inactivar' => 'CDLDAAI',
        'solicitud_activacion' => 'SOLACTI',
        'solicitud_activacion_aceptada' => 'ACTSOLACTI',
        'solicitud_activacion_rechazada' => 'RECHSOLACTI',
        'cambio_fecha_registro' => 'CAMFR',
    ];

    const COLOR = [
        'azul' => '#0071A1',
        'amarillo' => '#FFB119',
        'rojo' => '#f50000'
    ];

    const MENSAJE_INFORMATIVO_PARA_APROBAR = [
        'etapa' => 'PARA APROBAR, SE DEBE SELECCIONAR CUAL ES LA ETAPA QUE SEGUIRÁ UNA VEZ SE REALICE LA APROBACIÓN.',
        'firma' => 'SE REQUIERE LA FIRMA MECÁNICA DE TODOS LOS USUARIOS SELECCIONADOS PARA APROBAR.',
        'anulacion' => 'SE REQUIERE SELECCIONAR AL MENOS UNA ACTUACIÓN A ANULAR PARA PODER APROBARLO.',
    ];

    const TIPO_ARCHIVO = [
        'documento_inicial' => 'DOCINI',
        'documento_definitivo' => 'DOCFIN'
    ];

    const SALT = ['salt' => 'bdc695ab-7125-4f5d-8c72-8e68913406da'];

    const FASES_CON_DOCUMENTOS = [
        'soportes_radicado' => 5,
        'gestor_respuesta' => 9,
        'documento_cierre' => 8,
        'informe_cierre' => 18,
        'registro_seguimiento' => 19,
        'actuaciones' => 16,
    ];

    const ESTADOS_ELIMINADO = [
        'eliminado' => 1,
        'no_eliminado' => 0
    ];

    const TIPO_SUJETO_PROCESAL = [
        'Interesado' => 1,
    ];
}
