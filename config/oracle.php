<?php

return [
    'oracle' => [
        'driver'         => 'oracle',
        'tns'            => env('DB_TNS', ''),
        'host'           => env('DB_HOST', ''),
        'port'           => env('DB_PORT', '1521'),
        'database'       => env('DB_DATABASE', ''),
        'service_name'   => env('DB_SERVICENAME', ''),
        'username'       => env('DB_USERNAME', ''),
        'password'       => env('DB_PASSWORD', ''),
        'charset'        => env('DB_CHARSET', 'AL32UTF8'),
        'prefix'         => env('DB_PREFIX', ''),
        'prefix_schema'  => env('DB_SCHEMA_PREFIX', ''),
        'edition'        => env('DB_EDITION', 'ora$base'),
        'server_version' => env('DB_SERVER_VERSION', '11g'),
        'dynamic'        => [],
    ],

    'ORA_SINPROC' => [
        'driver'         => 'oracle',
        'tns'            => env('SINPROC_DB_TNS', ''),
        'host'           => env('SINPROC_DB_HOST', ''),
        'port'           => env('SINPROC_DB_PORT', '1521'),
        'database'       => env('SINPROC_DB_DATABASE', ''),
        'service_name'   => env('SINPROC_DB_SERVICENAME', ''),
        'username'       => env('SINPROC_DB_USERNAME', ''),
        'password'       => env('SINPROC_DB_PASSWORD', ''),
        'charset'        => env('SINPROC_DB_CHARSET', 'AL32UTF8'),
        'prefix'         => env('SINPROC_DB_PREFIX', ''),
        'prefix_schema'  => env('SINPROC_DB_SCHEMA_PREFIX', ''),
        'edition'        => env('SINPROC_DB_EDITION', 'ora$base'),
        'server_version' => env('SINPROC_DB_SERVER_VERSION', '11g'),
        'dynamic'        => [],
    ],
];
