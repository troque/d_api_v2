<?php

namespace App\Http\Enum;


enum TipoExpedienteEnum:int
{
    case DERECHO_PETICION = 1;
    case PODER_REFERENTE = 2;
    case QUEJA = 3;
    case TUTELA = 4;
}
