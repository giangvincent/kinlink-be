<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum RelationshipType: string
{
    use HasValues;

    case PARENT = 'PARENT';
    case SPOUSE = 'SPOUSE';
    case CHILD = 'CHILD';
    case OTHER = 'OTHER';
}
