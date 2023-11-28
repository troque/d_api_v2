<?php

namespace App\Http\Enum;

abstract class Suit{

    const PACIENTE_INFO_PERSONAL= 1;





}

enum Suit: string
{
    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'S';
}


