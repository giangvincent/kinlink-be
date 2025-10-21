<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum EventType: string
{
    use HasValues;

    case BIRTH = 'BIRTH';
    case DEATH = 'DEATH';
    case MARRIAGE = 'MARRIAGE';
    case ANNIV = 'ANNIV';
    case ANCESTOR_DAY = 'ANCESTOR_DAY';
}
