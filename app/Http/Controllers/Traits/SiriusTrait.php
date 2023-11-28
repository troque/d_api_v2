<?php

namespace App\Http\Controllers\Traits;

use App\Http\Requests\SearchRadicadoFormRequest;
use App\Http\Resources\Sirius\SearchRadicadoResource;
use App\Services\SiriusWS;
use stdClass;

trait SiriusTrait
{
    public static function buscarRadicado($datos)
    {
        $sirius = new SiriusWS();
        $respuesta = $sirius->login();
        if($respuesta->estado == false){
            return $respuesta;
        }

        $respuesta_consulta = $sirius->consultarRadicado($datos);

        if(!isset($respuesta_consulta->estado)){
            if(!$respuesta_consulta['correspondencia']){
                $error = new stdClass;
                $error->estado = false;
                $error->error = "No se encuentran resultados del numero de radicado digitado";
                return $error;
            }
            else{
                return $sirius->obtenerListaDocumentos($respuesta_consulta['correspondencia']['nroRadicado'], $respuesta_consulta['correspondencia']['idEcm']);
            }
        }
        else{
            return $respuesta_consulta;
        }
    }

    public static function buscarRadicado2($datos)
    {
        $sirius = new SiriusWS();
        $respuesta = $sirius->login();
        if($respuesta->estado == false){
            return $respuesta;
        }

        $respuesta_consulta = $sirius->consultarRadicado($datos);

        return $respuesta_consulta;

        if(!isset($respuesta_consulta->estado)){
            if(!$respuesta_consulta['correspondencia']){
                $error = new stdClass;
                $error->estado = false;
                $error->error = "No se encuentran resultados del numero de radicado digitado";
                return $error;
            }
        }
        else{
            return $respuesta_consulta;
        }
    }


    public static function buscarDocumento($id_documento, $versionLabel)
    {
        $sirius = new SiriusWS();
        $respuesta = $sirius->login();
        if($respuesta->estado == false){
            return $respuesta;
        }

        return $sirius->buscarDocumento($id_documento, $versionLabel);
    }

    public static function generarRadicado($datos)
    {
        $sirius = new SiriusWS();
        $respuesta = $sirius->login();
        if($respuesta->estado == false){
            return $respuesta;
        }

        return $sirius->radicacion($datos);
    }

    public static function subirDocumentoSirius($datos, $siriusTrackId){
        $sirius = new SiriusWS();
        $respuesta = $sirius->login();
        if($respuesta->estado == false){
            return $respuesta;
        }

        return $sirius->updateDocument($datos, $siriusTrackId);
    }

    public static function generarCuerpoPeticionSirius($datos, $interesado){
        //dd($interesado);
        //Paso 1: Datos de Correspondence
        $datosSirius['correspondence']['documentType'] = 'TL-DOCP';
        $datosSirius['correspondence']['description'] = $datos[0]['descripcion'];
        $datosSirius['correspondence']['attachDocument'] = true;
        $datosSirius['correspondence']['comunicationType'] = 'EE';
        $datosSirius['correspondence']['digitazionRequired'] = false;
        $datosSirius['correspondence']['electronicDistribution'] = false;
        $datosSirius['correspondence']['physicDistribution'] = false;
        $datosSirius['correspondence']['receptionChannel'] = 'Correo electrónico'; //NO PUEDE SER VETANILLA
        $datosSirius['correspondence']['requestSubsection'] = '15001';

        //Paso 2: Datos de listAgents
        $datosSirius['listAgents'][0]['agentType'] = 'Remitente';
        $datosSirius['listAgents'][0]['personType'] = $interesado->primer_nombre == 'ANÓNIMO(A)' ? 'Anonimo' : ($interesado->id_tipo_interesao == '1' ? 'Persona Natural' : 'Persona Juridica');

        if($interesado->primer_nombre != 'ANÓNIMO(A)'){
            $datosSirius['listAgents'][0]['identificationNumber'] = $interesado->numero_documento;
            $datosSirius['listAgents'][0]['name'] = $interesado->primer_nombre . ' ' . ($interesado->segundo_nombre ? ($interesado->segundo_nombre . ' ') : '') . $interesado->primer_apellido . ($interesado->segundo_apellido ? (' ' . $interesado->segundo_apellido) : '');
            $datosSirius['listAgents'][0]['person']['contactList'][0]['IsPrincipal'] = true;
            $datosSirius['listAgents'][0]['person']['contactList'][0]['address'] = $interesado->direccion_json != null ? $interesado->direccion_json : '';
            $datosSirius['listAgents'][0]['person']['contactList'][0]['cellphone'] = $interesado->telefono_celular;
            $datosSirius['listAgents'][0]['person']['contactList'][0]['contactType'] = 'Casa';
            $datosSirius['listAgents'][0]['person']['contactList'][0]['country'] = 'Colombia';
            $datosSirius['listAgents'][0]['person']['contactList'][0]['department'] = $interesado->nombre_departamento;
            $datosSirius['listAgents'][0]['person']['contactList'][0]['email'] = $interesado->email;
            $datosSirius['listAgents'][0]['person']['contactList'][0]['municipality'] = $interesado->nombre_ciudad;
            $datosSirius['listAgents'][0]['person']['contactList'][0]['phone'] = $interesado->telefono_fijo;
            $datosSirius['listAgents'][0]['person']['identificationNumber'] = $interesado->numero_documento;
            $datosSirius['listAgents'][0]['person']['identificationType'] = 'TP-DOCCC';
        }

        $datosSirius['listAgents'][1]['agentType'] = 'Destinatario';
        $datosSirius['listAgents'][1]['identificationNumber'] = '15001';

        //Paso 3: Datos de documentList
        $adjuntos = 0;
        $folios = 0;
        for($cont = 0; $cont < count($datos); $cont++){
            if(!$datos[$cont]['es_compulsa']){
                $adjuntos++;
                $folios += $datos[$cont]['num_folios'];
            }
        }

        $datosSirius['documentList'][0]['subject'] = $datos[0]['descripcion'];
        $datosSirius['documentList'][0]['foliosNumber'] = 1;
        $datosSirius['documentList'][0]['attachmentNumber'] = 1;
        // $datosSirius['documentList'][0]['documentDate'] = '2021-11-21T03:22:38.135Z';//date('Y-m-d\TH:i:s.Z\Z', time()); // '2021-11-21T03:22:38.135Z';
        $datosSirius['documentList'][0]['documentDate'] = date('c');//date('Y-m-d\TH:i:s.Z\Z', time()); // '2021-11-21T03:22:38.135Z';
        
        if(isset($datos[0]['fecha_documento'])){
            $datosSirius['documentList'][0]['documentDate'] = date('c', strtotime($datos[0]['fecha_documento']));
        }
        
        $datosSirius['documentList'][0]['documentType'] = 'TL-DOCOF';

        //Paso 4: Datos de referencedList
        $datosSirius['referencedList'] = [];

        //Paso 5: Datos de attachmentList
        for($cont = 0; $cont < count($datos); $cont++){
            if(!$datos[$cont]['es_compulsa']){
                $datosSirius['attachmentList'][$cont]['attachmentCode'] = 'ANE-EXP';
                $datosSirius['attachmentList'][$cont]['description'] = $datos[$cont]['nombre_archivo'];
                $datosSirius['attachmentList'][$cont]['supportTypeCode'] = 'TP-SOPE';
            }
        }

        return $datosSirius;

    }
}

