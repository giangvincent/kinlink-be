<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum PostVisibility: string
{
    use HasValues;

    case FAMILY = 'family';
    case GUESTS = 'guests';
}
