<?php

namespace App\Http\Utilidades;

enum UserRoleEnum:string
{
    case ADMIN = 'admin';
    case VISITOR = 'visitor';
    case EDITOR = 'editor';
}

abstract class Enum{

    const PACIENTE_INFO_PERSONAL= 1;


}

