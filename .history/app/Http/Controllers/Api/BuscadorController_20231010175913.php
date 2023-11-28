<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MigracionesTrait;
use Illuminate\Http\Request;
use App\Models\BuscadorModel;
use App\Http\Resources\Buscador\BuscadorCollection;
use App\Http\Resources\Buscador\BuscadorResource;
use App\Repositories\RepositoryGeneric;
use App\Http\Requests\BuscadorFormRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Traits\UtilidadesTrait;
use App\Http\Controllers\Traits\ValidarPermisosTrait;
use App\Http\Utilidades\Utilidades;

class BuscadorController extends Controller
{
    use UtilidadesTrait;
    use MigracionesTrait;
    use ValidarPermisosTrait;
    private $repository;

    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new BuscadorModel());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return BuscadorCollection::make($this->repository->paginate($request->limit ?? 20));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return BuscadorResource::make($this->repository->find($id));
    }

    public function buscadorGeneral(BuscadorFormRequest $request)
    {

        $datosRequest = $request->validated()["data"]["attributes"];

        /*if (!$this->validarNumeroCriteriosBusqueda($datosRequest)) {

            $error['estado'] = false;
            $error['error'] = 'DEBE SELECCIONAR AL MENOS DOS CRITERIOS DE BÚSQUEDA.';
            return json_encode($error);
        }*/

        /*
        $linea = "SELECT
        pd.radicado AS radicado,
        pd.uuid AS id,
        pd.vigencia AS vigencia,
        pd.created_at AS fecha,
        UPPER(mepd.nombre) AS estado,
        UPPER(mdo.nombre) AS dependencia,
        UPPER(mdd.nombre) AS dependencia_duena,
        UPPER(CONCAT(CONCAT(co.nombre, ' '), co.apellido)) AS usuario_comisionado,
        UPPER(me.nombre) AS etapa,
        UPPER(a.descripcion) AS descripcion,
        a.fecha_registro AS fecha_antecedente,
        UPPER(ei.nombre_investigado) AS nombre_investigado,
        UPPER(ei.cargo) AS cargo_investigado,
        UPPER(ei.entidad) AS entidad,
        UPPER(ei.sector) AS sector,
        UPPER(i.nombre) AS tipo_quejoso,
        UPPER(tsp.sujeto_procesal) AS sujeto_procesal,
        UPPER(CONCAT(CONCAT(i.primer_nombre, ' '), i.primer_apellido)) AS nombre_quejoso,
        i.numero_documento as numero_documento,
        UPPER(CONCAT(CONCAT(u.nombre, ' '), u.apellido)) AS funcionario_actual,
        UPPER(ev.nombre) AS evaluacion,
        UPPER(tcpd.nombre) AS tipo_de_conducta,
        act.auto,
        act.nombre_actuacion,
        u.name AS name_funcionario_actual
        FROM proceso_disciplinario pd
        INNER JOIN log_proceso_disciplinario lpd on lpd.id_proceso_disciplinario = pd.uuid
        OUTER APPLY (SELECT a.descripcion, a.fecha_registro FROM antecedente a WHERE a.id_proceso_disciplinario = pd.uuid ORDER BY a.created_at DESC) a
        INNER JOIN mas_etapa me on me.id = pd.id_etapa
        INNER JOIN mas_dependencia_origen mdo on mdo.id = pd.id_dependencia
        INNER JOIN mas_dependencia_origen mdd on mdd.id = pd.id_dependencia_duena
        OUTER APPLY (SELECT u.nombre, u.apellido, u.name FROM users u WHERE u.id = pd.usuario_comisionado) co
        INNER JOIN mas_estado_proceso_disciplinario mepd on mepd.id = pd.estado
        OUTER APPLY (SELECT u.nombre, u.apellido, u.name FROM users u WHERE u.name = lpd.id_funcionario_actual AND ROWNUM = 1 ORDER BY lpd.created_at DESC) u
        OUTER APPLY (SELECT ei.nombre_investigado, ent.nombre AS entidad, sec.nombre AS sector, sec.idsector FROM entidad_investigado ei INNER JOIN entidad ent ON ei.id_entidad = ent.identidad INNER JOIN sector sec ON ent.idsector = sec.idsector WHERE ei.id_proceso_disciplinario = pd.uuid AND ei.estado = 1 ORDER BY ei.created_at DESC) ei
        OUTER APPLY (SELECT i.primer_nombre, i.segundo_nombre, i.primer_apellido, i.segundo_apellido, i.id_tipo_interesao, mti.nombre FROM interesado i INNER JOIN mas_tipo_interesado mti on mti.id = i.id_tipo_interesao WHERE i.id_proceso_disciplinario = pd.uuid AND i.estado = 1  ORDER BY i.created_at DESC) i
        OUTER APPLY (SELECT mtsp.nombre AS sujeto_procesal FROM interesado i INNER JOIN mas_tipo_sujeto_procesal mtsp on mtsp.id = i.id_tipo_sujeto_procesal WHERE i.id_proceso_disciplinario = pd.uuid  ORDER BY i.created_at DESC) tsp
        OUTER APPLY (SELECT ev.tipo_conducta, mre.nombre FROM evaluacion ev INNER JOIN mas_resultado_evaluacion mre on mre.id = ev.resultado_evaluacion WHERE ev.id_proceso_disciplinario = pd.uuid AND (ev.estado = 2 or ev.estado = 3) ORDER BY ev.created_at DESC) ev
        OUTER APPLY (SELECT mtc.nombre FROM tipo_conducta_proceso_disciplinario tcpd INNER JOIN mas_tipo_conducta mtc on mtc.id = tcpd.id_tipo_conducta WHERE tcpd.id_proceso_disciplinario = pd.uuid ORDER BY tcpd.created_at DESC) tcpd
        OUTER APPLY (SELECT act.auto, ma.nombre_actuacion FROM actuaciones act INNER JOIN mas_actuaciones ma ON ma.id = act.id_actuacion WHERE act.uuid_proceso_disciplinario = pd.uuid AND act.id_estado_actuacion = 5 AND ROWNUM = 1 ORDER BY act.created_at DESC) act
        WHERE
        pd.radicado LIKE '%" . $datosRequest["radicado"] . "%'
        AND pd.vigencia LIKE '%" . $datosRequest["vigencia"] . "%'
        AND mepd.id LIKE '%" . $datosRequest["estado_expediente"] . "%'
        AND mdo.id LIKE '%" . $datosRequest["dependencia"] . "%'
        AND me.id LIKE '%" . $datosRequest["etapa"] . "%'
        AND (lpd.id_funcionario_actual IS NULL OR lpd.id_funcionario_actual LIKE '%" . $datosRequest["funcionario_actual"] . "%')
        AND (i.id_tipo_interesao IS NULL OR i.id_tipo_interesao LIKE '%" . $datosRequest["tipo_quejoso"] . "%')
        AND (i.id_tipo_sujeto_procesal IS NULL OR i.id_tipo_sujeto_procesal LIKE '%" . $datosRequest["sujeto_procesal"] . "%')
        AND UPPER(TRANSLATE(a.descripcion, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["antecedente"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou'))
        AND (ei.nombre_investigado IS NULL OR UPPER(TRANSLATE(ei.nombre_investigado, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["nombre_investigado"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
        AND (ei.cargo IS NULL OR ei.cargo LIKE '%" . $datosRequest["cargo_investigado"] . "%')
        AND (ei.id_entidad IS NULL OR ei.id_entidad LIKE '%" . $datosRequest["entidad"] . "%')
        AND (ei.idsector IS NULL OR ei.idsector LIKE '%" . $datosRequest["sector"] . "%')
        AND (i.primer_nombre IS NULL OR UPPER(TRANSLATE(i.primer_nombre, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["primer_nombre_quejoso"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
        AND (i.segundo_nombre IS NULL OR UPPER(TRANSLATE(i.segundo_nombre, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["segundo_nombre_quejoso"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
        AND (i.primer_apellido IS NULL OR UPPER(TRANSLATE(i.primer_apellido, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["primer_apellido_quejoso"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
        AND (i.segundo_apellido IS NULL OR UPPER(TRANSLATE(i.segundo_apellido, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["segundo_apellido_quejoso"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
        AND (i.numero_documento IS NULL OR UPPER(TRANSLATE(i.numero_documento, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["numero_documento"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
        AND (act.auto IS NULL OR UPPER(TRANSLATE(act.auto, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["auto"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
        AND (ev.resultado_evaluacion IS NULL OR ev.resultado_evaluacion LIKE '%" . $datosRequest["evaluacion"] . "%')
        AND (tcpd.id_tipo_conducta IS NULL OR tcpd.id_tipo_conducta LIKE '%" . $datosRequest["tipo_conducta"] . "%')
        ORDER BY pd.radicado DESC, pd.vigencia DESC";

        error_log($linea);
*/

        $datos = DB::select("SELECT
            pd.radicado AS radicado,
            pd.uuid AS id,
            pd.vigencia AS vigencia,
            pd.created_at AS fecha,
            UPPER(mepd.nombre) AS estado,
            UPPER(mdo.nombre) AS dependencia,
            mdo.id AS id_dependencia,
            UPPER(mdd.nombre) AS dependencia_duena,
            UPPER(CONCAT(CONCAT(co.nombre, ' '), co.apellido)) AS usuario_comisionado,
            UPPER(me.nombre) AS etapa,
            a.uuid AS id_antecedente,
            UPPER(a.descripcion) AS descripcion,
            a.fecha_registro AS fecha_antecedente,
            UPPER(ei.nombre_investigado) AS nombre_investigado,
            UPPER(ei.cargo) AS cargo_investigado,
            UPPER(ei.entidad) AS entidad,
            UPPER(ei.sector) AS sector,
            UPPER(i.nombre) AS tipo_quejoso,
            UPPER(tsp.sujeto_procesal) AS sujeto_procesal,
            UPPER(CONCAT(CONCAT(i.primer_nombre, ' '), i.primer_apellido)) AS nombre_quejoso,
            i.numero_documento as numero_documento,
            UPPER(CONCAT(CONCAT(u.nombre, ' '), u.apellido)) AS funcionario_actual,
            UPPER(ev.nombre) AS evaluacion,
            ev.resultado_evaluacion AS id_evaluacion,
            UPPER(tcpd.nombre) AS tipo_de_conducta,
            act.auto,
            act.nombre_actuacion,
            u.name AS name_funcionario_actual,
            (u2.nombre || ' ' || u2.apellido) AS registrado_por,
            pd.created_at,
            pdd.created_at AS fecha_ingreso_desgloce,
            pdpp.created_at AS fecha_ingreso_poder_preferente,
            pdsc.created_at AS fecha_ingreso_sinproc,
            pdss.created_at AS fecha_ingreso_sirius
            FROM proceso_disciplinario pd
            LEFT OUTER JOIN PROCESO_DESGLOSE pdd ON pdd.id_proceso_disciplinario = pd.uuid
            LEFT OUTER JOIN PROCESO_PODER_PREFERENTE pdpp ON pdpp.id_proceso_disciplinario = pd.uuid
            LEFT OUTER JOIN PROCESO_SINPROC pdsc ON pdsc.id_proceso_disciplinario = pd.uuid
            LEFT OUTER JOIN PROCESO_SIRIUS pdss ON pdss.id_proceso_disciplinario = pd.uuid
            INNER JOIN users u2 ON u2.name = pd.created_user
            INNER JOIN log_proceso_disciplinario lpd on lpd.id_proceso_disciplinario = pd.uuid
            OUTER APPLY (SELECT a.descripcion, a.fecha_registro FROM antecedente a WHERE a.id_proceso_disciplinario = pd.uuid ORDER BY a.created_at DESC) a
            INNER JOIN mas_etapa me on me.id = pd.id_etapa
            INNER JOIN mas_dependencia_origen mdo on mdo.id = pd.id_dependencia
            OUTER APPLY (SELECT mdd.nombre FROM mas_dependencia_origen mdd WHERE mdd.id = pd.id_dependencia_duena) mdd
            OUTER APPLY (SELECT u.nombre, u.apellido, u.name FROM users u WHERE u.id = pd.usuario_comisionado) co
            INNER JOIN mas_estado_proceso_disciplinario mepd on mepd.id = pd.estado
            OUTER APPLY (SELECT u.nombre, u.apellido, u.name FROM users u WHERE u.name = lpd.id_funcionario_actual AND ROWNUM = 1 ORDER BY lpd.created_at DESC) u
            OUTER APPLY (SELECT ei.nombre_investigado, ent.nombre AS entidad, sec.nombre AS sector, sec.idsector FROM entidad_investigado ei INNER JOIN entidad ent ON ei.id_entidad = ent.identidad INNER JOIN sector sec ON ent.idsector = sec.idsector WHERE ei.id_proceso_disciplinario = pd.uuid AND ei.estado = 1 ORDER BY ei.created_at DESC) ei
            OUTER APPLY (SELECT i.primer_nombre, i.segundo_nombre, i.primer_apellido, i.segundo_apellido, i.id_tipo_interesao, mti.nombre FROM interesado i INNER JOIN mas_tipo_interesado mti on mti.id = i.id_tipo_interesao WHERE i.id_proceso_disciplinario = pd.uuid AND i.estado = 1  ORDER BY i.created_at DESC) i
            OUTER APPLY (SELECT mtsp.nombre AS sujeto_procesal FROM interesado i INNER JOIN mas_tipo_sujeto_procesal mtsp on mtsp.id = i.id_tipo_sujeto_procesal WHERE i.id_proceso_disciplinario = pd.uuid  ORDER BY i.created_at DESC) tsp
            OUTER APPLY (SELECT ev.tipo_conducta, mre.nombre FROM evaluacion ev INNER JOIN mas_resultado_evaluacion mre on mre.id = ev.resultado_evaluacion WHERE ev.id_proceso_disciplinario = pd.uuid AND (ev.estado = 2 or ev.estado = 3) ORDER BY ev.created_at DESC) ev
            OUTER APPLY (SELECT mtc.nombre FROM tipo_conducta_proceso_disciplinario tcpd INNER JOIN mas_tipo_conducta mtc on mtc.id = tcpd.id_tipo_conducta WHERE tcpd.id_proceso_disciplinario = pd.uuid ORDER BY tcpd.created_at DESC) tcpd
            OUTER APPLY (SELECT act.auto, ma.nombre_actuacion FROM actuaciones act INNER JOIN mas_actuaciones ma ON ma.id = act.id_actuacion WHERE act.uuid_proceso_disciplinario = pd.uuid AND act.id_estado_actuacion = 5 AND ROWNUM = 1 ORDER BY act.created_at DESC) act
            WHERE
            pd.radicado LIKE '%" . $datosRequest["radicado"] . "%'
            AND pd.vigencia LIKE '%" . $datosRequest["vigencia"] . "%'
            AND mepd.id LIKE '%" . $datosRequest["estado_expediente"] . "%'
            AND mdo.id LIKE '%" . $datosRequest["dependencia"] . "%'
            AND me.id LIKE '%" . $datosRequest["etapa"] . "%'
            AND (lpd.id_funcionario_actual IS NULL OR lpd.id_funcionario_actual LIKE '%" . $datosRequest["funcionario_actual"] . "%')
            AND (i.id_tipo_interesao IS NULL OR i.id_tipo_interesao LIKE '%" . $datosRequest["tipo_quejoso"] . "%')
            AND (i.id_tipo_sujeto_procesal IS NULL OR i.id_tipo_sujeto_procesal LIKE '%" . $datosRequest["sujeto_procesal"] . "%')
            AND UPPER(TRANSLATE(a.descripcion, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["antecedente"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou'))
            AND (ei.nombre_investigado IS NULL OR UPPER(TRANSLATE(ei.nombre_investigado, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["nombre_investigado"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
            AND (ei.cargo IS NULL OR ei.cargo LIKE '%" . $datosRequest["cargo_investigado"] . "%')
            AND (ei.id_entidad IS NULL OR ei.id_entidad LIKE '%" . $datosRequest["entidad"] . "%')
            AND (ei.idsector IS NULL OR ei.idsector LIKE '%" . $datosRequest["sector"] . "%')
            AND (i.primer_nombre IS NULL OR UPPER(TRANSLATE(i.primer_nombre, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["primer_nombre_quejoso"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
            AND (i.segundo_nombre IS NULL OR UPPER(TRANSLATE(i.segundo_nombre, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["segundo_nombre_quejoso"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
            AND (i.primer_apellido IS NULL OR UPPER(TRANSLATE(i.primer_apellido, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["primer_apellido_quejoso"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
            AND (i.segundo_apellido IS NULL OR UPPER(TRANSLATE(i.segundo_apellido, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["segundo_apellido_quejoso"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
            AND (i.numero_documento IS NULL OR UPPER(TRANSLATE(i.numero_documento, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["numero_documento"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
            AND (act.auto IS NULL OR UPPER(TRANSLATE(act.auto, 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')) LIKE UPPER(TRANSLATE('%" . $datosRequest["auto"] . "%', 'ÁÉÍÓÚáéíóú', 'AEIOUaeiou')))
            AND (ev.resultado_evaluacion IS NULL OR ev.resultado_evaluacion LIKE '%" . $datosRequest["evaluacion"] . "%')
            AND (tcpd.id_tipo_conducta IS NULL OR tcpd.id_tipo_conducta LIKE '%" . $datosRequest["tipo_conducta"] . "%')
            ORDER BY pd.radicado DESC, pd.vigencia DESC");

        $datos_filtrados = []; // array resultante

        // Paso 1
        $datos_por_radicado = [];
        foreach ($datos as $objeto) {
            $radicado = $objeto->radicado;
            $datos_por_radicado[$radicado][] = $objeto;
        }

        // Paso 2
        foreach ($datos_por_radicado as $radicado => $objetos) {
            $hay_objeto_con_name_funcionario_actual_distinto_de_null = false;
            foreach ($objetos as $objeto) {
                if ($objeto->name_funcionario_actual !== null) {
                    $hay_objeto_con_name_funcionario_actual_distinto_de_null = true;
                    break;
                }
            }
            if ($hay_objeto_con_name_funcionario_actual_distinto_de_null) {
                foreach ($objetos as $objeto) {
                    if ($objeto->name_funcionario_actual !== null) {
                        $datos_filtrados[] = $objeto;
                        break; // Solo se agrega el primer objeto que cumple la condición
                    }
                }
            } else {
                $datos_filtrados[] = reset($objetos);
            }
        }


        //return json_encode($datos_filtrados);

        $array = array();

        for ($cont = 0; $cont < count($datos_filtrados); $cont++) {

            error_log("INVESTIGADO: " . $datos_filtrados[$cont]->nombre_investigado);

            if (
                ($datosRequest["funcionario_actual"] !== null &&  strlen($datos_filtrados[$cont]->funcionario_actual) === 1) ||
                ($datosRequest["nombre_investigado"] !== null &&  strlen($datos_filtrados[$cont]->nombre_investigado) === 0) ||
                ($datosRequest["cargo_investigado"] !== null &&  strlen($datos_filtrados[$cont]->cargo_investigado) === 0) ||
                ($datosRequest["primer_nombre_quejoso"] !== null &&  strlen($datos_filtrados[$cont]->nombre_quejoso) === 1) ||
                ($datosRequest["segundo_nombre_quejoso"] !== null &&  strlen($datos_filtrados[$cont]->nombre_quejoso) === 1) ||
                ($datosRequest["primer_apellido_quejoso"] !== null &&  strlen($datos_filtrados[$cont]->nombre_quejoso) === 1) ||
                ($datosRequest["segundo_apellido_quejoso"] !== null &&  strlen($datos_filtrados[$cont]->nombre_quejoso) === 1) ||
                ($datosRequest["tipo_quejoso"] !== null &&  strlen($datos_filtrados[$cont]->tipo_quejoso) === 0) ||
                ($datosRequest["numero_documento"] !== null &&  strlen($datos_filtrados[$cont]->numero_documento) === 0) ||
                ($datosRequest["sujeto_procesal"] !== null &&  strlen($datos_filtrados[$cont]->sujeto_procesal) === 0) ||
                ($datosRequest["evaluacion"] !== null &&  strlen($datos_filtrados[$cont]->evaluacion) === 0) ||
                ($datosRequest["auto"] !== null &&  strlen($datos_filtrados[$cont]->auto) === 0)
            ) {
            } else {

                $reciboDatos['attributes']['id'] = $datos_filtrados[$cont]->id;
                $reciboDatos['attributes']['radicado'] = $datos_filtrados[$cont]->radicado;
                $reciboDatos['attributes']['vigencia'] = $datos_filtrados[$cont]->vigencia;
                $reciboDatos['attributes']['fecha'] = Utilidades::getFormatoFechaDDMMYY($datos_filtrados[$cont]->fecha);
                $reciboDatos['attributes']['estado_expediente'] = $datos_filtrados[$cont]->estado;
                $reciboDatos['attributes']['dependencia'] = $datos_filtrados[$cont]->dependencia;
                $reciboDatos['attributes']['id_dependencia'] = $datos_filtrados[$cont]->id_dependencia;
                $reciboDatos['attributes']['etapa'] = $datos_filtrados[$cont]->etapa;
                $reciboDatos['attributes']['id_antecedente'] = $datos_filtrados[$cont]->id_antecedente;
                $reciboDatos['attributes']['antecedente'] = $datos_filtrados[$cont]->descripcion;
                $reciboDatos['attributes']['fecha_antecedente'] = Utilidades::getFormatoFechaDDMMYY($datos_filtrados[$cont]->fecha_antecedente);
                $reciboDatos['attributes']['antecedente_corto'] = Utilidades::getDescripcionCorta($datos_filtrados[$cont]->descripcion);
                $reciboDatos['attributes']['funcionario_actual'] = $datos_filtrados[$cont]->funcionario_actual;
                $reciboDatos['attributes']['log_funcionario_actual'] = $datos_filtrados[$cont]->name_funcionario_actual;


                if ($datos_filtrados[$cont]->nombre_investigado === null) {
                    $reciboDatos['attributes']['nombre_investigado'] = "NO_APLICA";
                } else {
                    $reciboDatos['attributes']['nombre_investigado'] = $datos_filtrados[$cont]->nombre_investigado;
                }

                if ($datos_filtrados[$cont]->cargo_investigado === null) {
                    $reciboDatos['attributes']['cargo_investigado'] = "NO_APLICA";
                } else {
                    $reciboDatos['attributes']['cargo_investigado'] = $datos_filtrados[$cont]->cargo_investigado;
                }

                if ($datos_filtrados[$cont]->entidad === null) {
                    $reciboDatos['attributes']['entidad'] = "NO_APLICA";
                } else {
                    $reciboDatos['attributes']['entidad'] = $datos_filtrados[$cont]->entidad;
                }

                if ($datos_filtrados[$cont]->sector === null) {
                    $reciboDatos['attributes']['sector'] = "NO_APLICA";
                } else {
                    $reciboDatos['attributes']['sector'] = $datos_filtrados[$cont]->sector;
                }

                if ($datos_filtrados[$cont]->numero_documento == '2030405060') {
                    $reciboDatos['attributes']['sujeto_procesal'] = "NO_APLICA";
                } else {
                    $reciboDatos['attributes']['sujeto_procesal'] = $datos_filtrados[$cont]->sujeto_procesal;
                }

                if ($datos_filtrados[$cont]->usuario_comisionado === null || $datos_filtrados[$cont]->usuario_comisionado === " ") {
                    $reciboDatos['attributes']['usuario_comisionado'] = "NO_APLICA";
                } else {
                    $reciboDatos['attributes']['usuario_comisionado'] = $datos_filtrados[$cont]->usuario_comisionado;
                }

                $reciboDatos['attributes']['dependencia_duena'] = $datos_filtrados[$cont]->dependencia_duena;

                $reciboDatos['attributes']['tipo_quejoso'] = $datos_filtrados[$cont]->tipo_quejoso;
                $reciboDatos['attributes']['nombre_quejoso'] = $datos_filtrados[$cont]->nombre_quejoso;
                $reciboDatos['attributes']['documento_quejoso'] = $datos_filtrados[$cont]->numero_documento;
                $reciboDatos['attributes']['evaluacion'] = $datos_filtrados[$cont]->evaluacion;
                $reciboDatos['attributes']['id_evaluacion'] = $datos_filtrados[$cont]->id_evaluacion;
                $reciboDatos['attributes']['tipo_de_conducta'] = $datos_filtrados[$cont]->tipo_de_conducta;
                $reciboDatos['attributes']['registrado_por'] = $datos_filtrados[$cont]->registrado_por;
                $reciboDatos['attributes']['created_at'] = Utilidades::getFormatoFechaDDMMYY($datos_filtrados[$cont]->created_at);
                $reciboDatos['attributes']['fecha_ingreso_desgloce'] = $datos_filtrados[$cont]->fecha_ingreso_desgloce ? Utilidades::getFormatoFechaDDMMYY($datos_filtrados[$cont]->fecha_ingreso_desgloce) : null;
                $reciboDatos['attributes']['fecha_ingreso_poder_preferente'] = $datos_filtrados[$cont]->fecha_ingreso_poder_preferente ? Utilidades::getFormatoFechaDDMMYY($datos_filtrados[$cont]->fecha_ingreso_poder_preferente) : null;
                $reciboDatos['attributes']['fecha_ingreso_sinproc'] = $datos_filtrados[$cont]->fecha_ingreso_sinproc ? Utilidades::getFormatoFechaDDMMYY($datos_filtrados[$cont]->fecha_ingreso_sinproc) : null;
                $reciboDatos['attributes']['fecha_ingreso_sirius'] = $datos_filtrados[$cont]->fecha_ingreso_sirius ? Utilidades::getFormatoFechaDDMMYY($datos_filtrados[$cont]->fecha_ingreso_sirius) : null;

                if (($this->validarUsuarioAsignado(auth()->user()->name, $datos_filtrados[$cont]->id) && $this->validarPermisoTrait(auth()->user()->name, 'CP_Asignado', 'Consultar')) ||
                    (!$this->validarUsuarioAsignado(auth()->user()->name,  $datos_filtrados[$cont]->id) && $this->validarPermisoTrait(auth()->user()->name, 'CP_NOAsignado', 'Consultar'))
                ) {

                    $reciboDatos['visible'] = true;
                    /*$reciboDatos['attributes']['nombre_investigado'] = $datos_filtrados[$cont]->nombre_investigado;
                    $reciboDatos['attributes']['cargo_investigado'] = $datos_filtrados[$cont]->cargo_investigado;
                    $reciboDatos['attributes']['tipo_quejoso'] = $datos_filtrados[$cont]->tipo_quejoso;
                    $reciboDatos['attributes']['nombre_quejoso'] = $datos_filtrados[$cont]->nombre_quejoso;
                    $reciboDatos['attributes']['documento_quejoso'] = $datos_filtrados[$cont]->numero_documento;
                    $reciboDatos['attributes']['evaluacion'] = $datos_filtrados[$cont]->evaluacion;
                    $reciboDatos['attributes']['tipo_de_conducta'] = $datos_filtrados[$cont]->tipo_de_conducta;
                    $reciboDatos['attributes']['auto'] = $datos_filtrados[$cont]->auto;
                    $reciboDatos['attributes']['nombre_actuacion'] = $datos_filtrados[$cont]->nombre_actuacion;
                    $reciboDatos['attributes']['sujeto_procesal'] = $datos_filtrados[$cont]->sujeto_procesal;*/
                } else {
                    $reciboDatos['visible'] = false;
                    /*$reciboDatos['attributes']['nombre_investigado'] = "";
                    $reciboDatos['attributes']['cargo_investigado'] = "";
                    $reciboDatos['attributes']['tipo_quejoso'] = "";
                    $reciboDatos['attributes']['nombre_quejoso'] = "";
                    $reciboDatos['attributes']['documento_quejoso'] = "";
                    $reciboDatos['attributes']['evaluacion'] = "";
                    $reciboDatos['attributes']['tipo_de_conducta'] = "";
                    $reciboDatos['attributes']['auto'] = "";
                    $reciboDatos['attributes']['nombre_actuacion'] = "";
                    $reciboDatos['attributes']['sujeto_procesal'] = "";
                    $reciboDatos['attributes']['entidad'] = "";
                    $reciboDatos['attributes']['sector'] = "";*/
                }

                array_push($array, $reciboDatos);
            }
        }

        $json['data'] = $array;
        return json_encode($json);
    }


    public function validarNumeroCriteriosBusqueda($datosRequest)
    {

        $suma = 0;

        if ($datosRequest["radicado"] != null) {
            $suma++;
        }
        if ($datosRequest["vigencia"] != null) {
            $suma++;
        }
        if ($datosRequest["estado_expediente"] != null) {
            $suma++;
        }
        if ($datosRequest["dependencia"] != null) {
            $suma++;
        }
        if ($datosRequest["etapa"] != null) {
            $suma++;
        }
        if ($datosRequest["antecedente"] != null) {
            $suma++;
        }
        if ($datosRequest["nombre_investigado"] != null) {
            $suma++;
        }
        if ($datosRequest["cargo_investigado"] != null) {
            $suma++;
        }
        if ($datosRequest["tipo_quejoso"] != null) {
            $suma++;
        }
        if ($datosRequest["primer_nombre_quejoso"] != null) {
            $suma++;
        }
        if ($datosRequest["segundo_nombre_quejoso"] != null) {
            $suma++;
        }
        if ($datosRequest["primer_apellido_quejoso"] != null) {
            $suma++;
        }
        if ($datosRequest["segundo_apellido_quejoso"] != null) {
            $suma++;
        }
        if ($datosRequest["numero_documento"] != null) {
            $suma++;
        }
        if ($datosRequest["sujeto_procesal"] != null) {
            $suma++;
        }
        if ($datosRequest["funcionario_actual"] != null) {
            $suma++;
        }
        if ($datosRequest["evaluacion"] != null) {
            $suma++;
        }
        if ($datosRequest["tipo_conducta"] != null) {
            $suma++;
        }
        if ($datosRequest["entidad"] != null) {
            $suma++;
        }
        if ($datosRequest["sector"] != null) {
            $suma++;
        }


        if ($suma >= 2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     */
    public function buscadorMigracion()
    {


        $request_expediente['fechaRegistroDesde'] = null;
        $request_expediente['fechaRegistroHasta'] = null;
        $request_expediente['version'] = null;
        $request_expediente['vigencia'] = "";
        $request_expediente['numeroRadicado'] = "308232";
        $request_expediente['nombreResponsable'] = "";
        $request_expediente['idResponsable'] = "";
        $request_expediente['dependencia'] = "";
        $request_expediente['idDependencia'] = "";
        $request_expediente['tipoInteresado'] = "";

        //$request = json_encode($request_expediente);

        //error_log($request);

        return $this->buscarExpediente($request_expediente);
        //return $this->buscarExpedientePorNumeroRadicado("308232");

    }
}
