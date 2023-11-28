<?php

namespace App\Http\Utilidades;

enum UserRoleEnum:string
{
    case ADMIN = 'admin';
    case VISITOR = 'visitor';
    case EDITOR = 'editor';
}



