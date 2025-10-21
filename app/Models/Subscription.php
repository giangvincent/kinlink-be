<?php

namespace App\Models;

use App\Enums\SubscriptionProvider;
use App\Enums\SubscriptionStatus;
use App\Models\Concerns\BelongsToFamily;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use BelongsToFamily;
    use HasFactory;

    protected $fillable = [
        'family_id',
        'provider',
        'status',
        'current_period_end',
        'seats',
        'storage_quota_mb',
    ];

    protected function casts(): array
    {
        return [
            'provider' => SubscriptionProvider::class,
            'status' => SubscriptionStatus::class,
            'current_period_end' => 'datetime',
            'seats' => 'integer',
            'storage_quota_mb' => 'integer',
        ];
    }
}
