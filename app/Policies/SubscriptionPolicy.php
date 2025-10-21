<?php

namespace App\Policies;

use App\Enums\FamilyRole;
use App\Models\Subscription;
use App\Models\User;
use App\Policies\Concerns\HandlesFamilyAuthorization;

class SubscriptionPolicy
{
    use HandlesFamilyAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER);
    }

    public function view(User $user, Subscription $subscription): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER, $subscription);
    }

    public function create(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER);
    }

    public function update(User $user, Subscription $subscription): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER, $subscription);
    }

    public function delete(User $user, Subscription $subscription): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER, $subscription);
    }
}
