<?php

namespace App\Http\Enum;

abstract class Enum{

    const TIPO_EXPEDIENTE = [
        'derecho_peticion' => 1,
        'poder_referente' => 2,
        'queja' => 3,
        'tutela' => 4
    ];


    const TIPO_DERECHO_PETICION = [
        'copias' => 1,
        'general' => 2,
        'alerta_control_politico' => 3
    ];

    const TIPO_QUEJA = [
        'externa' => 1,
        'interna' => 2
    ];

}


