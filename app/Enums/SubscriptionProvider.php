<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum SubscriptionProvider: string
{
    use HasValues;

    case STRIPE = 'stripe';
    case LOCAL = 'local';
}
