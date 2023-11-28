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

    public static function buscarRadicado2($datos)
    {
        $sirius = new SiriusWS();
        $sirius->login();

        $respuesta_consulta = $sirius->consultarRadicado($datos);

        error_log($respuesta_consulta->correspondencia);

        if(!isset($respuesta_consulta->correspondencia)){
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
            $datosSirius['listAgents'][0]['name'] = $interesado->primer_nombre . ' ' . $interesado->primer_apellido;
            //$datosSirius['listAgents'][0]['person']['personId'] = 0; // TEMPORAL
            $datosSirius['listAgents'][0]['person']['contactList'][0]['IsPrincipal'] = true;
            $datosSirius['listAgents'][0]['person']['contactList'][0]['address'] = "{\"tipoVia\":{\"id\":2,\"codigo\":\"TP-VIACL\",\"nombre\":\"Carrera\",\"codPadre\":\"TP-VIA\",\"estado\":\"A \"},\"noViaPrincipal\":\"92\",\"prefijoCuadrante\":{\"id\":8,\"codigo\":\"PE-CUAA\",\"nombre\":\"A\",\"codPadre\":\"PE-CUAD\",\"estado\":\"A \"},\"noVia\":\"40\",\"prefijoCuadrante_se\":{\"id\":8,\"codigo\":\"PE-CUAA\",\"nombre\":\"A\",\"codPadre\":\"PE-CUAD\",\"estado\":\"A \"},\"placa\":\"33\",\"orientacion_se\":{\"id\":86,\"codigo\":\"ORIEN-S\",\"nombre\":\"Sur\",\"codPadre\":\"ORIEN\",\"estado\":\"A \"}}";
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
        $datosSirius['documentList'][0]['documentDate'] = '2021-11-21T03:22:38.135Z';//date('Y-m-d\TH:i:s.Z\Z', time()); // '2021-11-21T03:22:38.135Z';
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

        //dd($datosSirius);

        return $datosSirius;

    }
}

