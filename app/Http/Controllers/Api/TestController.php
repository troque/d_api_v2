<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function __construct() {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $array = array(
            "METHOD" => "GET",
            "TEST" => "Variables de archivo env...",
            "APP_ENV" => env("APP_ENV"),
            "APP_URL" => env("APP_URL"),
            "DB_HOST" => env("DB_HOST"),
            "DB_PORT" => env("DB_PORT"),
            "DB_DATABASE" => env("DB_DATABASE"),
            "DB_SERVICENAME" => env("DB_SERVICENAME"),
            "DB_USERNAME" => env("DB_USERNAME"),
            "DB_PASSWORD" => env("DB_PASSWORD"),
            "ADMIN_USER" => env("ADMIN_USER"),

        );

        return $array;
    }

    public function store(Request $request)
    {
        $array = array(
            "METHOD" => "POST",
            "TEST" => "Variables de archivo env...",
            "APP_ENV" => env("APP_ENV"),
            "APP_URL" => env("APP_URL"),
            "DB_HOST" => env("DB_HOST"),
            "DB_PORT" => env("DB_PORT"),
            "DB_DATABASE" => env("DB_DATABASE"),
            "DB_SERVICENAME" => env("DB_SERVICENAME"),
            "DB_USERNAME" => env("DB_USERNAME"),
            "DB_PASSWORD" => env("DB_PASSWORD"),
            "ADMIN_USER" => env("ADMIN_USER"),

        );

        return $array;
    }

   
}
