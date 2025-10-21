<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum PersonGender: string
{
    use HasValues;

    case FEMALE = 'female';
    case MALE = 'male';
    case NON_BINARY = 'non_binary';
    case OTHER = 'other';
    case UNKNOWN = 'unknown';
}
