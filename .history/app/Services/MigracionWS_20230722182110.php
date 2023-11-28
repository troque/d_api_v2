<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use stdClass;

class MigracionWS
{
    private $token = "";

    /**
     *
     */
    public function login()
    {
        try {
            $url = env("BASE_URL_MIGRACION") . "/api/authorization/Authenticate";

            $datos['username'] = env("USER_MIGRACION");
            $datos['password'] = env("PASS_MIGRACION");

            error_log("USERNAME " . $datos['username']);
            error_log("PASSWORD " . $datos['password']);

            $response = Http::withBasicAuth(env("USER_MIGRACION"), env("PASS_MIGRACION"))
                ->withHeaders(["Soadoc-Token" => $this->token])
                ->post($url, $datos);

            error_log("TOKEN: " . $this->token);

            if ($response->successful()) {
                $data = $response->collect()->all();
                $this->token = $data["token"];
            }

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un problema con la comunicación con Migración, si el error persiste comuniquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    /**
     *
     */
    public function consultarRadicado($data)
    {
        try {

            $url = env("BASE_URL_MIGRACION") . "/GetDataMultiSource";

            error_log("URL: " . $url);
            error_log("TOKEN: " . $this->token);

            $response = Http::withoutVerifying()
                ->withToken($this->token)
                ->post($url, $data);

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error para obtener la información de Migración, si el error persiste comuniquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    /**
     *
     */
    public function consultarExpediente($expediente, $vigencia)
    {
        try {

            $url = env("BASE_URL_MIGRACION") . "/GetProcesoDisciplinario?radicado=" . $expediente . "&vigencia=" . $vigencia;

            //error_log("URL: " . $url);
            //error_log("TOKEN: " . $this->token);

            $response = Http::withoutVerifying()
                ->withToken($this->token)
                ->post($url);

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error para obtener la información de Migración, si el error persiste comuniquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    /**
     *
     */
    public function consultarExpedienteVersionUno($expediente)
    {
        try {

            $url = env("BASE_URL_MIGRACION") . "/DetalleV1?numSolicitud=$expediente";

            error_log("URL: " . $url);
            error_log("TOKEN: " . $this->token);

            $response = Http::withoutVerifying()
                ->withToken($this->token)
                ->post($url);

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error para obtener la información de Migración, si el error persiste comuniquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    /**
     *
     */
    public function consultarExpedienteVersionDos($expediente)
    {
        try {

            $url = env("BASE_URL_MIGRACION") . "/DetalleV2?numSolicitud=$expediente";

            error_log("URL: " . $url);
            error_log("TOKEN: " . $this->token);

            $response = Http::withoutVerifying()
                ->withToken($this->token)
                ->post($url);

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error para obtener la información de Migración, si el error persiste comuníquese con el Administrador";
                return $error;
            }
        } catch (Exception  $e) {
            $error = new stdClass;
            $error->estado = false;
            $error->error = $e->getMessage();
            return $error;
        }
    }

    /**
     *
     */
    public function consultarExpedienteVersionTres($expediente)
    {
        try {

            $url = env("BASE_URL_MIGRACION") . "/GetAllXlsByExpediente?expediente=$expediente";

            error_log("URL: " . $url);
            error_log("TOKEN: " . $this->token);

            $response = Http::withoutVerifying()
                ->withToken($this->token)
                ->post($url);

            if ($response->status() == 200 || $response->status() == 201) {
                return $response->collect()->all();
            } else {
                $error = new stdClass;
                $error->estado = false;
                $error->error = "Ha ocurrido un error para obtener la información de Migración, si el error persiste comuniquese con el Administrador";
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
