<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use stdClass;

class SiriusWS
{
    private $token = "";

    public function login()
    {
        try {
            error_log("Login Sirius");
            $url = env("BASE_URL_SIRIUS") . "/soaint-sgd-web-api-gateway/apis/securidad-gateway-api/login";

            error_log($url);

            $response = Http::withoutVerifying()->post($url, [
                "login" => env("USER_SIRIUS"),
                "password" => env("PASS_SIRIUS")
            ]);

            error_log($response->status());

            if ($response->successful()) {
                $data = $response->collect()->all();
                $this->token = $data["token"];
                $error = new stdClass;
                $error->estado = true;
                return $error;
            } else if ($response->status() == 401) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 401: Falla la validacion de las credenciales con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            error_log("Error:". $e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    public function radicacion($request)
    {
        try {
            error_log("Generar radicacion");
            $url = env("BASE_URL_SIRIUS", "prueba") . "/correspondence-api/soaint-soadoc-core-enterprise/v1/tracks";

            error_log($url);

            $response = Http::withBasicAuth(env("USER_SIRIUS"), env("PASS_SIRIUS"))
                ->withHeaders(["Soadoc-Token" => $this->token])
                ->timeout(60)
                ->post($url, $request);

            error_log($response->status());

            //print_r(json_encode($request));

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else if ($response->status() == 400) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 400: Algun parametro de la solicitud a SIRIUS no ha sido enviado de manera correcta, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 401) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 401: Falla la validacion de las credenciales con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 500) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 500: Error interno de solicitud con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            error_log("Error:". $e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    public function updateDocument($request, $siriusTrackId)
    {
        try {
            error_log("Subir documento");
            $url = env("BASE_URL_SIRIUS") . "/correspondence-api/soaint-soadoc-core-enterprise/v1/tracks/$siriusTrackId/principal";
            
            error_log($url);

            $response = Http::withBasicAuth(env("USER_SIRIUS"), env("PASS_SIRIUS"))
                ->withHeaders(["Soadoc-Token" => $this->token])
                ->timeout(60);

            foreach ($request as $file) {
                $response->attach("file", file_get_contents($file['path']), $file['nombre']);
            }

            $response = $response->post($url);

            error_log($response->status());

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else if ($response->status() == 400) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 400: Algun parametro de la solicitud a SIRIUS no ha sido enviado de manera correcta, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 401) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 401: Falla la validacion de las credenciales con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 500) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 500: Error interno de solicitud con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else {
                $error = new stdClass;
                $error->estado = false;
                error_log($response->status());
                $error->error = "Ha ocurrido un error al adjuntar documentos con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            error_log("Error:". $e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
        /*try {
            error_log("Subir documento");
            $url = env("BASE_URL_SIRIUS") . "/correspondence-api/soaint-soadoc-core-enterprise/v1/tracks/$siriusTrackId/principal";
            
            error_log($url);
        
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        
            $headers = [
                'Soadoc-Token: ' . $this->token,
                'Authorization: Basic ' . base64_encode(env("USER_SIRIUS") . ':' . env("PASS_SIRIUS")),
            ];
        
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        
            // Adjuntar archivos
            foreach ($request as $file) {
                $postFile = curl_file_create($file['path'], '', $file['nombre']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => $postFile]);
            }
        
            $response = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
            error_log("HTTP Status: $httpStatus");
        
            if ($httpStatus == 200 || $httpStatus == 201) {
                $responseData = json_decode($response, true);
                return $responseData;
            } elseif ($httpStatus == 400) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 400: Algun parametro de la solicitud a SIRIUS no ha sido enviado de manera correcta, si el error persiste comuníquese con el Administrador";
                return $error;
            } elseif ($httpStatus == 401) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 401: Falla la validacion de las credenciales con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } elseif ($httpStatus == 500) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 500: Error interno de solicitud con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else {
                $error = new stdClass;
                $error->estado = false;
                error_log($httpStatus);
                $error->error = "Ha ocurrido un error al adjuntar documentos con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            }
        
            curl_close($ch);
        } catch (Exception $e) {
            error_log("Error:" . $e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }*/
    }

    public function consultarRadicado($siriusTrackId)
    {
        try {
            error_log("Consultar Radicado");
            $url = env("BASE_URL_SIRIUS") . "/soaint-sgd-web-api-gateway/apis/correspondencia-gateway-api/obtener-comunicacion/$siriusTrackId";

            error_log($url);

            $response = Http::withoutVerifying()
                ->withToken($this->token)
                ->get($url);

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else if ($response->status() == 400) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 400: Algun parametro de la solicitud a SIRIUS no ha sido enviado de manera correcta, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 401) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 401: Falla la validacion de las credenciales con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 500) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 500: Error interno de solicitud con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            error_log("Error:". $e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    public function obtenerListaDocumentos($nroRadicado, $idEcm)
    {
        try {
            error_log("Obtener lista de documentos");
            $url = env("BASE_URL_SIRIUS") . "/soaint-sgd-web-api-gateway/apis/digitalizar-documento-gateway-api/obtener-documentos-asociados-radicado/$nroRadicado?allDocuments=false&idDocumento=$idEcm&allAnnexes=true";
            
            error_log($url);

            $response = Http::withoutVerifying()
                ->withToken($this->token)
                ->get($url);

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else if ($response->status() == 400) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 400: Algun parametro de la solicitud a SIRIUS no ha sido enviado de manera correcta, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 401) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 401: Falla la validacion de las credenciales con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 500) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 500: Error interno de solicitud con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            error_log("Error:". $e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    public function searchRadicado($radicado_sirius)
    {
        try {
            error_log("Buscar radicado");
            $url = env("BASE_URL_SIRIUS") . "/soaint-sgd-web-api-gateway/apis/digitalizar-documento-gateway-api/obtener-documentos-asociados-radicado/$radicado_sirius";

            error_log($url);

            $response = Http::withoutVerifying()
                ->withToken($this->token)
                ->get($url);

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else if ($response->status() == 400) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 400: Algun parametro de la solicitud a SIRIUS no ha sido enviado de manera correcta, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 401) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 401: Falla la validacion de las credenciales con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 500) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 500: Error interno de solicitud con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            error_log("Error:". $e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    public function buscarDocumento($id_documento, $versionLabel)
    {
        try {
            error_log("Buscar documento");
            $url = env("BASE_URL_SIRIUS") . "/ecm-integration-services/apis/ecm/descargarDocumentoVersionECM/?identificadorDoc=$id_documento&version=$versionLabel";

            error_log($url);

            $response = Http::withoutVerifying()
                ->withToken($this->token)
                ->get($url);

            if ($response->status() == 200 || $response->status() == 201) {
                $datos['content_type'] = "content-type: " . $response->headers()['Content-Type'][0];
                $datos['base_64'] = base64_encode($response->body());
                return $datos;
            } else if ($response->status() == 400) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 400: Algun parametro de la solicitud a SIRIUS no ha sido enviado de manera correcta, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 401) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 401: Falla la validacion de las credenciales con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else if ($response->status() == 500) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 500: Error interno de solicitud con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            error_log("Error:". $e);
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }
}
