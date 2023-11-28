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
            $url = env("BASE_URL_SIRIUS") . "/soaint-sgd-web-api-gateway/apis/securidad-gateway-api/login";

            $response = Http::withoutVerifying()->post($url, [
                "login" => env("USER_SIRIUS"),
                "password" => env("PASS_SIRIUS")
            ]);

            if ($response->successful()) {
                $data = $response->collect()->all();
                $this->token = $data["token"];
                $error = new stdClass;
                $error->estado = true;
                return $error;
            } else if ($response->status() == 401) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 401: Falla la validacion de las credenciales con SIRIUS, si el error persiste comuniquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    public function radicacion($request)
    {
        try {
            $url = env("BASE_URL_SIRIUS", "prueba") . "/correspondence-api/soaint-soadoc-core-enterprise/v1/tracks";

            $response = Http::withBasicAuth(env("USER_SIRIUS"), env("PASS_SIRIUS"))
                ->withHeaders(["Soadoc-Token" => $this->token])
                ->post($url, $request);

            //print_r(json_encode($request));

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else if ($response->status() == 400) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 400: Algun parametro de la solicitud a SIRIUS no ha sido enviado de manera correcta, si el error persiste comuniquese con el Administrador";
                return $error;
            } else if ($response->status() == 401) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 401: Falla la validacion de las credenciales con SIRIUS, si el error persiste comuniquese con el Administrador";
                return $error;
            } else if ($response->status() == 500) {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Error 500: Error interno de solicitud con SIRIUS, si el error persiste comuniquese con el Administrador";
                return $error;
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error con SIRIUS, si el error persiste comuniquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    public function updateDocument($request, $siriusTrackId)
    {
        try {
            $url = env("BASE_URL_SIRIUS") . "/correspondence-api/soaint-soadoc-core-enterprise/v1/tracks/$siriusTrackId/principal?stamp=true";

            $response = Http::withBasicAuth(env("USER_SIRIUS"), env("PASS_SIRIUS"))
                ->withHeaders(["Soadoc-Token" => $this->token]);

            foreach ($request as $file) {
                $response->attach("file", file_get_contents($file['path']), $file['nombre']);
            }

            $response = $response->post($url);

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
                $error->error = "Ha ocurrido un error al adjuntar documentos con SIRIUS, si el error persiste comuníquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    public function consultarRadicado($siriusTrackId)
    {
        try {

            $url = env("BASE_URL_SIRIUS") . "/soaint-sgd-web-api-gateway/apis/correspondencia-gateway-api/obtener-comunicacion/$siriusTrackId";

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
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    public function obtenerListaDocumentos($nroRadicado, $idEcm)
    {
        try {

            $url = env("BASE_URL_SIRIUS") . "/soaint-sgd-web-api-gateway/apis/digitalizar-documento-gateway-api/obtener-documentos-asociados-radicado/$nroRadicado?allDocuments=false&idDocumento=$idEcm&allAnnexes=true";

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
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    public function searchRadicado($radicado_sirius)
    {
        try {
            //$url = env("BASE_URL_SIRIUS") . "/soaint-sgd-web-api-gateway/apis/correspondencia-gateway-api/consultar-radicado/$radicado_sirius";
            //$url = env("BASE_URL_SIRIUS") . "/soaint-sgd-web-api-gateway/apis/correspondencia-gateway-api/obtener-comunicacion/$radicado_sirius";
            //{{Server}}/soaint-sgd-web-api-gateway/apis/digitalizar-documento-gateway-api/obtener-documentos-asociados-radicado/2022-ER-0119430?allDocuments=false&idDocumento=188f7716-3edb-44f4-9c9d-b5b4e5a4d8b6&allAnnexes=true
            $url = env("BASE_URL_SIRIUS") . "/soaint-sgd-web-api-gateway/apis/digitalizar-documento-gateway-api/obtener-documentos-asociados-radicado/$radicado_sirius";

            // error_log($url);

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
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    public function buscarDocumento($id_documento, $versionLabel)
    {
        try {

            $url = env("BASE_URL_SIRIUS") . "/ecm-integration-services/apis/ecm/descargarDocumentoVersionECM/?identificadorDoc=$id_documento&version=$versionLabel";

            // error_log($url);

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
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }
}
