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
        $sirius->login();

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

    public static function buscarDocumento($id_documento, $versionLabel)
    {
        $sirius = new SiriusWS();
        $sirius->login();
        return $sirius->buscarDocumento($id_documento, $versionLabel);
    }

    public static function generarRadicado($datos)
    {
        $sirius = new SiriusWS();
        $sirius->login();
        return $sirius->radicacion($datos);
    }

    public static function subirDocumentoSirius($datos, $siriusTrackId){
        $sirius = new SiriusWS();
        $sirius->login();
        return $sirius->updateDocument($datos, $siriusTrackId);
    }

    public static function generarCuerpoPeticionSirius($datos){
        //Paso 1: Datos de Correspondence
        $datosSirius['correspondence']['documentType'] = 'TL-DOCOF';
        $datosSirius['correspondence']['description'] = 'Puede que la tarea que me ';
        $datosSirius['correspondence']['responseTime'] = 0;
        $datosSirius['correspondence']['attachDocument'] = true;
        $datosSirius['correspondence']['comunicationType'] = 'EE';
        $datosSirius['correspondence']['dateRequest'] = '2021-11-24T09:29:58.000Z';
        $datosSirius['correspondence']['dateDocument'] = '2021-11-24T09:29:58.000Z';
        $datosSirius['correspondence']['digitazionRequired'] = false;
        $datosSirius['correspondence']['electronicDistribution'] = false;
        $datosSirius['correspondence']['physicDistribution'] = false;
        $datosSirius['correspondence']['receptionChannel'] = 'Correo Electrónico';
        $datosSirius['correspondence']['requestSubsection'] = '12223';

        //Paso 2: Datos de listAgents
        $datosSirius['listAgents'][0]['agentType'] = 'Remitente';
        $datosSirius['listAgents'][0]['identificationNumber'] = 900234980;
        $datosSirius['listAgents'][0]['name'] = 'Coonfecciones el eden S.A.A';
        $datosSirius['listAgents'][0]['personType'] = 'Persona Juridica';
        $datosSirius['listAgents'][0]['person']['personId'] = 0;
        $datosSirius['listAgents'][0]['person']['contactList'][0]['IsPrincipal'] = true;
        $datosSirius['listAgents'][0]['person']['contactList'][0]['address'] = "{\"tipoVia\":{\"id\":2,\"codigo\":\"TP-VIACL\",\"nombre\":\"Carrera\",\"codPadre\":\"TP-VIA\",\"estado\":\"A \"},\"noViaPrincipal\":\"92\",\"prefijoCuadrante\":{\"id\":8,\"codigo\":\"PE-CUAA\",\"nombre\":\"A\",\"codPadre\":\"PE-CUAD\",\"estado\":\"A \"},\"noVia\":\"40\",\"prefijoCuadrante_se\":{\"id\":8,\"codigo\":\"PE-CUAA\",\"nombre\":\"A\",\"codPadre\":\"PE-CUAD\",\"estado\":\"A \"},\"placa\":\"33\",\"orientacion_se\":{\"id\":86,\"codigo\":\"ORIEN-S\",\"nombre\":\"Sur\",\"codPadre\":\"ORIEN\",\"estado\":\"A \"}}";
        $datosSirius['listAgents'][0]['person']['contactList'][0]['cellphone'] = 3157487514;
        $datosSirius['listAgents'][0]['person']['contactList'][0]['contactType'] = 'Trabajo';
        $datosSirius['listAgents'][0]['person']['contactList'][0]['country'] = 'Colombia';
        $datosSirius['listAgents'][0]['person']['contactList'][0]['department'] = 'La Guajira';
        $datosSirius['listAgents'][0]['person']['contactList'][0]['email'] = 'ericksaavedra6@gmail.com';
        $datosSirius['listAgents'][0]['person']['contactList'][0]['municipality'] = null;
        $datosSirius['listAgents'][0]['person']['contactList'][0]['phone'] = 4578545;
        $datosSirius['listAgents'][0]['person']['identificationNumber'] = 900234980;
        $datosSirius['listAgents'][0]['person']['identificationType'] = 'TP-DOCN';
        $datosSirius['listAgents'][0]['person']['name'] = 'Coonfecciones el eden S.A.A';
        $datosSirius['listAgents'][0]['person']['personType'] = 'Persona Juridica';
        $datosSirius['listAgents'][1]['agentType'] = 'Destinatario';
        $datosSirius['listAgents'][1]['identificationNumber'] = '12223';

        //Paso 3: Datos de documentList
        $datosSirius['documentList'][0]['subject'] = 'Puede que la tarea que me ';
        $datosSirius['documentList'][0]['foliosNumber'] = 1;
        $datosSirius['documentList'][0]['attachmentNumber'] = 1;
        $datosSirius['documentList'][0]['documentDate'] = '2021-11-21T03:22:38.135Z';
        $datosSirius['documentList'][0]['documentType'] = 'TL-DOCOF';

        //Paso 4: Datos de referencedList
        $datosSirius['referencedList'] = [];

        //Paso 5: Datos de attachmentList
        $cont = 0;
        foreach($datos as $dato){
            if(!isset($dato['es_compulsa']) && !$dato['es_compulsa']){
                $datosSirius['attachmentList'][$cont]['attachmentCode'] = 'ANE-EXP';
                $datosSirius['attachmentList'][$cont]['description'] = $dato['nombre_archivo'];
                $datosSirius['attachmentList'][$cont]['supportTypeCode'] = 'TP-SOPE';
                $cont++;
            }
        }

        return $datosSirius;

    }
}

