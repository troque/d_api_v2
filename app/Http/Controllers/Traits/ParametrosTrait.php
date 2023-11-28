<?php

namespace App\Http\Controllers\Traits;

use App\Http\Resources\ArchivoActuaciones\ArchivoActuacionesResource;
use App\Http\Resources\TrazabilidadActuaciones\TrazabilidadActuacionesResource;
use App\Models\ActuacionesModel;
use App\Models\ArchivoActuacionesModel;
use App\Models\MasParametroCamposModel;
use App\Models\TrazabilidadActuacionesModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

trait ParametrosTrait
{

    /**
     * Metodo encargado de consultar las firmas de las actuaciones
     */
    public function obtenerConsultasParametros($parametros, $id_proceso_disciplinario)
    {
        //Obtener todos los parametros
        $parametros = MasParametroCamposModel::whereIn('type', $parametros)->where('estado', 1)->get();

        $datosTabla = array();

        $arrayAux = null;

        foreach ($parametros as $parametro) {
            if ($parametro->referencia_tabla) {
                $index = funcionEncontrarTabla($datosTabla, $parametro);
                if ($index === false) {
                    $arrayAux['ultimo_dato'] = $parametro->referencia_ultimo_dato;
                    $arrayAux['nombre_campo'] = $parametro->type;
                    $arrayAux['tabla'] = $parametro->referencia_tabla;
                    $arrayAux['grupo_general'] = $parametro->grupo_general;
                    $arrayAux['columnas'] = array();
                    $arrayColumnaAux['principal'] = $parametro->principal;
                    $arrayColumnaAux['columna'] = $parametro->referencia_columna;
                    $arrayColumnaAux['columna_consulta'] = $parametro->referencia_columna_maestra_consulta;
                    $arrayColumnaAux['referencia'] = $parametro->type;
                    $arrayColumnaAux['tabla_consulta'] = $parametro->referencia_tabla_maestra ? $parametro->referencia_tabla_maestra : $parametro->referencia_tabla;
                    $arrayColumnaAux['tabla_join'] = $parametro->referencia_tabla_maestra ? true : false;
                    $arrayColumnaAux['columna_join'] = $parametro->referencia_columna_maestra;
                    array_push($arrayAux['columnas'], $arrayColumnaAux);
                    array_push($datosTabla, $arrayAux);
                } else {
                    $arrayColumnaAux['principal'] = $parametro->principal;
                    $arrayColumnaAux['columna'] = $parametro->referencia_columna;
                    $arrayColumnaAux['columna_consulta'] = $parametro->referencia_columna_maestra_consulta;
                    $arrayColumnaAux['referencia'] = $parametro->type;
                    $arrayColumnaAux['tabla_consulta'] = $parametro->referencia_tabla_maestra ? $parametro->referencia_tabla_maestra : $parametro->referencia_tabla;
                    $arrayColumnaAux['tabla_join'] = $parametro->referencia_tabla_maestra ? true : false;
                    $arrayColumnaAux['columna_join'] = $parametro->referencia_columna_maestra;
                    array_push($datosTabla[$index]['columnas'], $arrayColumnaAux);
                }
            }
        }

        //dd($datosTabla);

        $arrayAux = null;

        //Obtener los datos necesarios
        $resultados = array();
        foreach ($datosTabla as $tabla) {

            $select = 'SELECT ';
            $innerJoin = ' ';
            $arrayAux['principal'] = null;

            foreach ($tabla['columnas'] as $index => $columna) {
                
                if ($columna['principal'] === '1') {
                    $arrayAux['principal'] = $columna['referencia'];
                }

                if (strpos($columna['columna'], ',') !== false) {
                    $columna['columna'] = str_replace(",", " || ' ' || ", $columna['columna']);
                }

                $select .= "UPPER(" . $columna['tabla_consulta'] . '.' . ($columna['tabla_join'] ? $columna['columna_consulta'] : $columna['columna']) . ') AS ' . $columna['referencia'];
                if ($index < (count($tabla['columnas']) - 1)) {
                    $select .= ', ';
                }

                if ($columna['tabla_join']) {
                    $innerJoin .= ' LEFT OUTER JOIN ' . $columna['tabla_consulta'] . ' ON ' . $tabla['tabla'] . '.' . $columna['columna'] . ' = ' . $columna['tabla_consulta'] . '.' . $columna['columna_join'];
                }
            }

            $select .= ' FROM ' . $tabla['tabla'];
            $select .= $innerJoin;
            if ($tabla['tabla'] == 'proceso_disciplinario') {
                $select .= " WHERE UUID = '" . $id_proceso_disciplinario . "'";
            } else {
                $select .= " WHERE ID_PROCESO_DISCIPLINARIO = '" . $id_proceso_disciplinario . "'";
            }
            $select .= " AND " . $tabla['tabla'] . ".ESTADO = 1";
            $select .= " ORDER BY " . $columna['tabla_consulta'] . ".created_at DESC";

            $arrayAux['referencia'] = $tabla['tabla'];
            $arrayAux['grupo_general'] = $tabla['grupo_general'];

            $arrayAux['resultados'] = DB::select($select);
            if ($arrayAux['resultados'] && $tabla['ultimo_dato']) {
                $arrayAux['resultados'] = $arrayAux['resultados'][0];
            }

            array_push($resultados, $arrayAux);
        }

        $arrayAux = null;
        //dd($resultados);
        $ordenamientoPrimerNivel = array();
        foreach ($resultados as $resultado) {
            $grupo = 1;
            $posicionDeseada = 0;
            for ($cont = 0; $cont < (count($resultado['resultados'])); $cont++) {
                foreach ($resultado['resultados'][$cont] as $index => $fila) {
                    $arrayAux['grupo_general'] = $resultado['grupo_general'];
                    $arrayAux['grupo'] = $resultado['grupo_general'] . '_' . $grupo;
                    $arrayAux['dato'] = $fila;
                    $arrayAux['parametro'] = $index;
                    $arrayAux['principal'] = $resultado['principal'] == $index ? true : false;
                    $arrayAux['crear_tabla'] = $resultado['principal'] ? true : false;
                    $arrayAux['seleccionar'] = true;
                    $arrayAux['index'] = $cont;

                    if($resultado['principal'] == $index){
                        if($posicionDeseada == 0){
                            array_unshift($ordenamientoPrimerNivel, $arrayAux);
                        }
                        else{
                            // Dividir el array en dos partes
                            $arrayAux2[0] = $arrayAux;
                            $parte1 = array_slice($ordenamientoPrimerNivel, 0, $posicionDeseada);
                            $parte2 = array_slice($ordenamientoPrimerNivel, $posicionDeseada);
                            $ordenamientoPrimerNivel = array_merge($parte1, $arrayAux2, $parte2);
                        }
                        $posicionDeseada++;
                    }
                    else{
                        array_push($ordenamientoPrimerNivel, $arrayAux);
                    }
                }
                $grupo++;
            }
        }

        for($cont = 0; $cont < count($ordenamientoPrimerNivel); $cont++){
            foreach($parametros AS $parametro){
                if($ordenamientoPrimerNivel[$cont]['parametro'] === $parametro['type']){
                    $ordenamientoPrimerNivel[$cont]['orden'] = $parametro['orden'];
                    break;
                }
            }
        }
        
        usort($ordenamientoPrimerNivel, organizarResultados('orden'));
        //dd($ordenamientoPrimerNivel);
        
        $datosAgrupados = array_reduce($ordenamientoPrimerNivel, function ($resultado, $item) {
            $parametro = $item["parametro"];

            // Si el parametro aún no está en el resultado, lo inicializamos como un array vacío
            if (!isset($resultado[$parametro])) {
                $resultado[$parametro] = [];
            }

            // Agregamos el elemento actual al array del parametro correspondiente
            $resultado[$parametro][] = $item;

            return $resultado;
        }, []);

        return $datosAgrupados;
    }   

    public function organizarParametros($parametros){
        //Obtener todos los parametros
        $parametrosQuery = MasParametroCamposModel::whereIn('type', $parametros)->where('estado', 1)->orderBy('orden')->get();
        $resultado = [];
        for($cont = 0; $cont < count($parametrosQuery); $cont++){
            $resultado[$cont] = $parametrosQuery[$cont]['type'];
        }
        return $resultado;
    }
}

function organizarResultados($clave,$orden=null) {
    return function ($a, $b) use ($clave, $orden) {
        $result=  ($orden=="DESC") ? strnatcmp($b[$clave], $a[$clave]) :  strnatcmp($a[$clave], $b[$clave]);
        return $result;
    };
}

function funcionEncontrarTabla($array, $parametro)
{
    foreach ($array as $index => $value) {
        if ($value["tabla"] === $parametro->referencia_tabla && $value["grupo_general"] == $parametro->grupo_general) {
            return $index;
        }
    }
    return false;
}