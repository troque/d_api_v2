<?php

namespace App\Models;

use App\Http\Utilidades\Constants;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MasEstadoActuaciones;
use App\Models\MasActuacionesModel;
use Illuminate\Support\Facades\DB;

class ActuacionesModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "actuaciones";

    public $timestamps = true;

    protected $fillable = [
        "id_actuacion",
        "usuario_accion",
        "id_estado_actuacion",
        "documento_ruta",
        "created_user",
        "updated_user",
        "deleted_user",
        "estado",
        "uuid_proceso_disciplinario",
        "id_etapa",
        "id_dependencia",
        "auto",
        "campos_finales",
        "created_at",
        "id_estado_visibilidad",
        "fecha_registro"
    ];

    protected $hidden = [
        "updated_at",
        "deleted_at",
        "updated_user",
        "deleted_user",
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    public function mas_actuaciones()
    {
        return $this->belongsTo(MasActuacionesModel::class, "id_actuacion", "id");
    }

    public function mas_estado_actuaciones()
    {
        return $this->belongsTo(MasEstadoActuacionesModel::class, "id_estado_actuacion", "id");
    }

    public function proceso_disciplinario()
    {
        return $this->belongsTo(ProcesoDiciplinarioModel::class, "uuid_proceso_disciplinario", "uuid");
    }

    public function etapa()
    {
        return $this->belongsTo(EtapaModel::class, "id_etapa", "id");
    }


    public function etapa_siguiente($id_etapa)
    {
        return EtapaModel::where([
            ['id', '=', $id_etapa],
        ])->take(1)->first();
    }

    public function dependencia()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "id_dependencia", "id");
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, "created_user", "name");
    }

    public function actuacion_inactiva($id_actuacion)
    {
        $dato = ActuacionInactivaModel::where('id_actuacion', $id_actuacion)->get();
        if (count($dato) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function actuacion_inactiva_principal($id_actuacion)
    {
        $dato = ActuacionInactivaModel::where('id_actuacion_principal', $id_actuacion)->get();
        if (count($dato) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function actuacion()
    {
        $datos = $this->hasOne(MasActuacionesModel::class, "id", "id_actuacion")->get();
        return $datos[0]->nombre_actuacion;
    }

    public function datos_usuario()
    {
        $usuario = $this->hasOne(User::class, "name", "created_user")->get();
        $datos['nombre_usuario'] = $usuario[0]->nombre;
        $datos['apellido_usuario'] = $usuario[0]->apellido;
        return $datos;
    }

    public function usuarioDatosEspecificos()
    {
        return $this->belongsTo(User::class, "created_user", "name")->select('id', 'name', 'email', 'nombre', 'apellido');
    }

    public function archivos($idActuacion)
    {
        $archivos = DB::select("
            SELECT
                aa.uuid,
                aa.id_tipo_archivo,
                aa.created_user,
                aa.nombre_archivo,
                aa.extension,
                aa.peso,
                aa.eliminado,
                aa.documento_ruta,
                mtaa.nombre AS nombre_tipo_archivo,
                mtaa.codigo AS codigo_tipo_archivo,
                mtaa.descripcion AS descripcion_tipo_archivo
            FROM
                archivo_actuaciones aa
            INNER JOIN mas_tipo_archivo_actuaciones mtaa ON aa.id_tipo_archivo = mtaa.id
            WHERE aa.uuid_actuacion = '$idActuacion'
        ");

        return $archivos;
    }

    private function mostrarCuadroAdvertencia ($masActuacion){
        if (
            $masActuacion->etapa_siguiente === '1' ||
            $masActuacion->cierra_proceso === '1' ||
            $masActuacion->excluyente === '1' ||
            $masActuacion->despues_aprobacion_listar_actuacion === '1'
        ) {
            return true;
        }

        return false;
    }

    public function acciones($codigo, $masActuacion, $etapaSiguiente, $firmas, $actuacionInactivaPrincipal, $procesoDisciplinario, $dependenciaSegundaInstancia)
    {

        $migrado = $procesoDisciplinario->migrado;
        $tipoProceso = $procesoDisciplinario->id_tipo_proceso;

        $acciones['mostrar_etapa_despues_aprobacion'] = false;
        $acciones['mostrar_lista_actuaciones_inactivar'] = false;
        $acciones['mostrar_input_carga_archivo_borrador'] = false;
        $acciones['mostrar_input_carga_archivo_definitivo'] = false;
        $acciones['mostrar_boton_aprobar_actuacion'] = false;
        $acciones['mostrar_boton_rechazar_actuacion'] = false;
        $acciones['mostrar_boton_aprobar_impedimento'] = false;
        $acciones['mostrar_boton_aprobar_solicitud_inactivacion'] = false;
        $acciones['mostrar_boton_aprobar_solicitud_activacion'] = false;
        $acciones['mostrar_firmas'] = false;
        $acciones['mostrar_historial'] = false;
        $acciones['mostrar_boton_agregar_semaforo'] = false;
        $acciones['mostrar_boton_accion_fecha_semaforo'] = false;
        $acciones['mostrar_boton_accion_transacciones'] = true;
        $acciones['mostrar_boton_accion_solicitud_inactivacion'] = false;
        $acciones['mostrar_boton_accion_solicitud_activacion'] = false;
        $acciones['mostrar_boton_visiblidad'] = false;
        $acciones['mostrar_cuadro_advertencia'] = false;
        $acciones['mostrar_mensaje_informativo_para_aprobar'] = [];
        $acciones['color_estado'] = Constants::COLOR['azul'];
        $acciones['mostrar_boton_guardar_auto'] = false;
        $acciones['editar_fecha_registro'] = false;

        if($migrado === '1' || $tipoProceso == Constants::TIPO_DE_PROCESO['poder_preferente'] || $dependenciaSegundaInstancia){
           $acciones['mostrar_boton_guardar_auto'] = true;
        }

        if ($codigo == Constants::ESTADO_ACTUACION['aprobada']) {
            $acciones['mostrar_input_carga_archivo_definitivo'] = true;
            $acciones['mostrar_boton_accion_transacciones'] = true;
            $acciones['color_estado'] = Constants::COLOR['azul'];
            $acciones['mostrar_boton_guardar_auto'] = false;
        } else if ($codigo == Constants::ESTADO_ACTUACION['rechazada']) {
            $acciones['color_estado'] = Constants::COLOR['rojo'];
            $acciones['mostrar_boton_guardar_auto'] = false;
        } else if ($codigo == Constants::ESTADO_ACTUACION['pendiente_aprobacion']) {
            $acciones['editar_fecha_registro'] = true;
            if ($masActuacion->etapa_siguiente) {
                $acciones['mostrar_etapa_despues_aprobacion'] = true;
            }
            if ($masActuacion->despues_aprobacion_listar_actuacion) {
                $acciones['mostrar_lista_actuaciones_inactivar'] = true;
            }

            $acciones['mostrar_cuadro_advertencia'] = $this->mostrarCuadroAdvertencia($masActuacion);

            if (
                $masActuacion->etapa_siguiente === '1' && $etapaSiguiente == null
            ) {
                $arrayMensaje['tipo'] = 'etapa';
                $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['etapa'];
                array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
            }
            if($actuacionInactivaPrincipal == false && $masActuacion->despues_aprobacion_listar_actuacion === '1'){
                $arrayMensaje['tipo'] = 'anulacion';
                $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['anulacion'];
                array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
            }

            foreach ($firmas as $firma) {
                if ($firma->estado === strval(Constants::ESTADO_FIRMA_MECANICA['pendiente_de_firma'])) {
                    $arrayMensaje['tipo'] = 'firma';
                    $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['firma'];
                    array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
                    break;
                }
            }

            $acciones['mostrar_input_carga_archivo_borrador'] = true;
            $acciones['mostrar_boton_aprobar_actuacion'] = true;
            $acciones['mostrar_boton_aprobar_impedimento'] = true;
            $acciones['mostrar_boton_rechazar_actuacion'] = true;
            $acciones['mostrar_firmas'] = true;
            $acciones['mostrar_historial'] = true;
            $acciones['mostrar_boton_agregar_semaforo'] = true;
            $acciones['mostrar_boton_accion_transacciones'] = true;
            $acciones['mostrar_boton_visiblidad'] = true;
            $acciones['color_estado'] = Constants::COLOR['amarillo'];
        } else if ($codigo == Constants::ESTADO_ACTUACION['solicitud_inactivacion']) {
            $acciones['mostrar_boton_aprobar_solicitud_inactivacion'] = true;
            $acciones['color_estado'] = Constants::COLOR['amarillo'];
            $acciones['mostrar_boton_guardar_auto'] = false;
        } else if ($codigo == Constants::ESTADO_ACTUACION['aprobada_pdf_definitivo']) {
            $acciones['mostrar_boton_accion_solicitud_inactivacion'] = true;
            $acciones['mostrar_boton_guardar_auto'] = false;
        } else if ($codigo == Constants::ESTADO_ACTUACION['actualizacion_documento']) {
            $acciones['editar_fecha_registro'] = true;
            $acciones['mostrar_boton_accion_transacciones'] = true;
            $acciones['mostrar_boton_visiblidad'] = true;
            $acciones['mostrar_boton_agregar_semaforo'] = true;
            $acciones['color_estado'] = Constants::COLOR['amarillo'];
            $acciones['mostrar_historial'] = true;

            $acciones['mostrar_cuadro_advertencia'] = $this->mostrarCuadroAdvertencia($masActuacion);

            if (
                $masActuacion->etapa_siguiente === '1' && $etapaSiguiente == null
            ) {
                $arrayMensaje['tipo'] = 'etapa';
                $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['etapa'];
                array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
            }
            if($actuacionInactivaPrincipal == false && $masActuacion->despues_aprobacion_listar_actuacion === '1'){
                $arrayMensaje['tipo'] = 'anulacion';
                $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['anulacion'];
                array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
            }

            foreach ($firmas as $firma) {
                if ($firma->estado === strval(Constants::ESTADO_FIRMA_MECANICA['pendiente_de_firma'])) {
                    $arrayMensaje['tipo'] = 'firma';
                    $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['firma'];
                    array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
                    break;
                }
            }
        } else if ($codigo == Constants::ESTADO_ACTUACION['solicitud_inactivacion_aceptada']) {
            $acciones['mostrar_boton_accion_solicitud_activacion'] = true;
            $acciones['mostrar_boton_guardar_auto'] = false;
        } else if ($codigo == Constants::ESTADO_ACTUACION['solicitud_inactivacion_rechazada']) {
            $acciones['mostrar_boton_accion_solicitud_inactivacion'] = true;
            $acciones['mostrar_boton_guardar_auto'] = false;
        } else if ($codigo == Constants::ESTADO_ACTUACION['documento_firmado']) {
            $acciones['mostrar_input_carga_archivo_definitivo'] = true;
            $acciones['mostrar_boton_accion_transacciones'] = true;
            $acciones['mostrar_boton_visiblidad'] = true;
            $acciones['mostrar_boton_agregar_semaforo'] = true;
            $acciones['color_estado'] = Constants::COLOR['amarillo'];
            $acciones['mostrar_historial'] = true;

            $acciones['mostrar_cuadro_advertencia'] = $this->mostrarCuadroAdvertencia($masActuacion);

            if (
                $masActuacion->etapa_siguiente === '1' && $etapaSiguiente == null
            ) {
                $arrayMensaje['tipo'] = 'etapa';
                $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['etapa'];
                array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
            }
            if($actuacionInactivaPrincipal == false && $masActuacion->despues_aprobacion_listar_actuacion === '1'){
                $arrayMensaje['tipo'] = 'anulacion';
                $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['anulacion'];
                array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
            }

            foreach ($firmas as $firma) {
                if ($firma->estado === strval(Constants::ESTADO_FIRMA_MECANICA['pendiente_de_firma'])) {
                    $arrayMensaje['tipo'] = 'firma';
                    $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['firma'];
                    array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
                    break;
                }
            }

        } else if ($codigo == Constants::ESTADO_ACTUACION['actuación_inactivada']) {
            $acciones['mostrar_boton_accion_solicitud_activacion'] = true;
            $acciones['mostrar_boton_guardar_auto'] = false;
        } else if ($codigo == Constants::ESTADO_ACTUACION['cambio_etapa']) {
            $acciones['mostrar_firmas'] = true;
            $acciones['mostrar_boton_accion_transacciones'] = true;
            $acciones['mostrar_boton_visiblidad'] = true;
            $acciones['mostrar_boton_agregar_semaforo'] = true;
            $acciones['color_estado'] = Constants::COLOR['amarillo'];
            $acciones['mostrar_historial'] = true;
            $acciones['mostrar_cuadro_advertencia'] = $this->mostrarCuadroAdvertencia($masActuacion);

            if (
                $masActuacion->etapa_siguiente === '1' && $etapaSiguiente == null
            ) {
                $arrayMensaje['tipo'] = 'etapa';
                $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['etapa'];
                array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
            }
            if($actuacionInactivaPrincipal == false && $masActuacion->despues_aprobacion_listar_actuacion === '1'){
                $arrayMensaje['tipo'] = 'anulacion';
                $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['anulacion'];
                array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
            }

            foreach ($firmas as $firma) {
                if ($firma->estado === strval(Constants::ESTADO_FIRMA_MECANICA['pendiente_de_firma'])) {
                    $arrayMensaje['tipo'] = 'firma';
                    $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['firma'];
                    array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
                    break;
                }
            }

        } else if ($codigo == Constants::ESTADO_ACTUACION['cambio_lista_actuaciones_inactivar']) {
            $acciones['mostrar_firmas'] = true;
            $acciones['mostrar_boton_accion_transacciones'] = true;
            $acciones['mostrar_boton_visiblidad'] = true;
            $acciones['mostrar_boton_agregar_semaforo'] = true;
            $acciones['color_estado'] = Constants::COLOR['amarillo'];
            $acciones['mostrar_historial'] = true;

            $acciones['mostrar_cuadro_advertencia'] = $this->mostrarCuadroAdvertencia($masActuacion);

            if (
                $masActuacion->etapa_siguiente === '1' && $etapaSiguiente == null
            ) {
                $arrayMensaje['tipo'] = 'etapa';
                $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['etapa'];
                array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
            }
            if($actuacionInactivaPrincipal == false && $masActuacion->despues_aprobacion_listar_actuacion === '1'){
                $arrayMensaje['tipo'] = 'anulacion';
                $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['anulacion'];
                array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
            }

            foreach ($firmas as $firma) {
                if ($firma->estado === strval(Constants::ESTADO_FIRMA_MECANICA['pendiente_de_firma'])) {
                    $arrayMensaje['tipo'] = 'firma';
                    $arrayMensaje['mensaje'] = Constants::MENSAJE_INFORMATIVO_PARA_APROBAR['firma'];
                    array_push($acciones['mostrar_mensaje_informativo_para_aprobar'], $arrayMensaje);
                    break;
                }
            }

        } else if ($codigo == Constants::ESTADO_ACTUACION['solicitud_activacion']) {
            $acciones['mostrar_boton_aprobar_solicitud_activacion'] = true;
            $acciones['mostrar_boton_guardar_auto'] = false;
            $acciones['color_estado'] = Constants::COLOR['amarillo'];
        } else if ($codigo == Constants::ESTADO_ACTUACION['solicitud_activacion_aceptada']) {
            $acciones['mostrar_boton_accion_solicitud_inactivacion'] = true;
            $acciones['mostrar_boton_guardar_auto'] = false;
        } else if ($codigo == Constants::ESTADO_ACTUACION['solicitud_activacion_rechazada']) {
            $acciones['mostrar_boton_accion_solicitud_activacion'] = true;
            $acciones['mostrar_boton_guardar_auto'] = false;
        }

        return $acciones;
    }

    public function firmas($idActuacion, $auto, $documentoRuta)
    {
        if ($documentoRuta) {
            $documentoRuta = $documentoRuta[count($documentoRuta) - 1]->documento_ruta;
        }

        $archivos = DB::select("
            SELECT
                fa.id,
                fa.id_actuacion,
                fa.id_user,
                fa.tipo_firma,
                fa.estado,
                fa.eliminado,
                mtf.id AS tipo_firma_id,
                mtf.nombre AS tipo_firma_nombre,
                u.id AS usuario_id,
                u.name AS usuario_name,
                u.nombre AS usuario_nombre,
                u.apellido AS usuario_apellido,
                u.firma_mecanica AS usuario_firma_mecanica,
                u.password_firma_mecanica AS usuario_password_firma_mecanica,
                '$auto' AS actuacion_auto,
                '$documentoRuta'AS actuacion_documento_ruta
            FROM
                firma_actuaciones fa
            INNER JOIN mas_tipo_firma mtf ON fa.tipo_firma = mtf.id
            INNER JOIN users u ON u.id = fa.id_user
            WHERE fa.id_actuacion = '$idActuacion'
            AND (fa.eliminado IS NULL OR eliminado = 0)
            ORDER BY fa.created_at DESC
        ");

        return $archivos;
    }

    public function trazabilidadPrimerRegistro($idActuacion)
    {
        $archivos = DB::select(
            "
                SELECT
                    u.name,
                    u.nombre AS nombre_usuario,
                    u.apellido AS apellido_usuario,
                    mdo.nombre AS nombre_dependencia
                FROM
                    trazabilidad_actuaciones ta
                INNER JOIN mas_dependencia_origen mdo ON mdo.id = ta.id_dependencia
                INNER JOIN users u ON u.NAME = ta.created_user
                WHERE TA.uuid_actuacion = '$idActuacion'
                ORDER BY ta.created_at ASC
            "
        );

        if(count($archivos) > 0){
            return $archivos[0];
        }

        return null;

    }

    public function dependenciaSegundaInstancia()
    {
        $dependencia = DB::select(
            "
                SELECT
                    mdo.nombre
                FROM
                mas_dependencia_origen mdo
                INNER JOIN mas_dependencia_configuracion mdc ON mdo.id = mdc.id_dependencia_origen
                WHERE mdc.id_dependencia_acceso = 16
                AND mdo.id = " . auth()->user()->id_dependencia . "
            "
        );

        if(count($dependencia) > 0){
            return true;
        }

        return false;
    }

}
