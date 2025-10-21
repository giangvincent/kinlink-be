<?php

namespace App\Policies;

use App\Enums\FamilyRole;
use App\Models\Event;
use App\Models\User;
use App\Policies\Concerns\HandlesFamilyAuthorization;

class EventPolicy
{
    use HandlesFamilyAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::GUEST);
    }

    public function view(User $user, Event $event): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::GUEST, $event);
    }

    public function create(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER);
    }

    public function update(User $user, Event $event): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER, $event);
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER, $event);
    }
}
