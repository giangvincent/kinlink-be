<?php

namespace App\Services;

use App\Enums\BillingPlan;
use App\Enums\SubscriptionProvider;
use App\Models\Family;
use App\Models\Subscription;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class BillingService
{
    public function plan(Family $family): array
    {
        $subscription = $family->subscriptions()->latest()->first();

        return [
            'current_plan' => $family->billing_plan instanceof BillingPlan
                ? $family->billing_plan->value
                : $family->billing_plan,
            'subscription' => $subscription ? [
                'provider' => $subscription->provider?->value ?? $subscription->provider,
                'status' => $subscription->status?->value ?? $subscription->status,
                'current_period_end' => $subscription->current_period_end?->toIso8601String(),
                'seats' => $subscription->seats,
                'storage_quota_mb' => $subscription->storage_quota_mb,
            ] : null,
        ];
    }

    public function checkout(Family $family, string $plan, int $seats): array
    {
        $sessionId = Str::uuid()->toString();

        return [
            'checkout_url' => sprintf('https://billing.example.com/checkout/%s', $sessionId),
            'session_id' => $sessionId,
        ];
    }

    public function handleStripeWebhook(array $payload): void
    {
        $eventType = Arr::get($payload, 'type');

        if ($eventType === 'checkout.session.completed') {
            $familyId = Arr::get($payload, 'data.object.metadata.family_id');
            $plan = Arr::get($payload, 'data.object.metadata.plan', BillingPlan::FREE->value);
            $seats = (int) Arr::get($payload, 'data.object.metadata.seats', 0);
            $storage = (int) Arr::get($payload, 'data.object.metadata.storage_quota_mb', 0);

            if ($familyId) {
                $family = Family::find($familyId);

                if ($family) {
                    $family->billing_plan = BillingPlan::from($plan);
                    $family->save();

                    Subscription::updateOrCreate(
                        [
                            'family_id' => $family->getKey(),
                            'provider' => SubscriptionProvider::STRIPE,
                        ],
                        [
                            'status' => SubscriptionStatus::ACTIVE,
                            'current_period_end' => now()->addMonth(),
                            'seats' => $seats,
                            'storage_quota_mb' => $storage,
                        ]
                    );
                }
            }
        }
    }

    public function usage(Family $family): array
    {
        $storageBytes = $family->members()->count() * 5 * 1024 * 1024;

        return [
            'members_count' => $family->members()->count(),
            'storage_bytes' => $storageBytes,
            'storage_quota_mb' => $family->subscriptions()->latest()->value('storage_quota_mb') ?? 0,
        ];
    }
}
