<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum PersonVisibility: string
{
    use HasValues;

    case PRIVATE = 'private';
    case FAMILY = 'family';
    case GUESTS = 'guests';
}
