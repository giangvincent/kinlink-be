<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum BillingPlan: string
{
    use HasValues;

    case FREE = 'free';
    case STANDARD = 'standard';
    case PREMIUM = 'premium';
}
