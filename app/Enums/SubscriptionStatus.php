<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum SubscriptionStatus: string
{
    use HasValues;

    case ACTIVE = 'active';
    case TRIALING = 'trialing';
    case PAST_DUE = 'past_due';
    case CANCELED = 'canceled';
    case INCOMPLETE = 'incomplete';
    case INCOMPLETE_EXPIRED = 'incomplete_expired';
    case PAUSED = 'paused';
}
