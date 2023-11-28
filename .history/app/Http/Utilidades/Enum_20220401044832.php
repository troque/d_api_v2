<?php

namespace App\Http\Enum;

abstract class Enum{

    const TIPO_EXPEDIENTE = [
        'Derecho_peticion' => 1,
        'Poder_referente' => 2,
        'Queja' => 3,
        'Tutela' => 4
    ];


    const TIPO_QUEJA = [
        'Externa' => 1,
        'Interna' => 2
    ];



}


