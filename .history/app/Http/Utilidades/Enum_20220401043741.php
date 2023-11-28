<?php

namespace App\Http\Enum;

abstract class Enum{

    enum Suit: string
{
    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'S';
}



}


