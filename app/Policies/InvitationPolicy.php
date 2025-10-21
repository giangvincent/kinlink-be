<?php

namespace App\Policies;

use App\Enums\FamilyRole;
use App\Models\Invitation;
use App\Models\User;
use App\Policies\Concerns\HandlesFamilyAuthorization;

class InvitationPolicy
{
    use HandlesFamilyAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER);
    }

    public function view(User $user, Invitation $invitation): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER, $invitation);
    }

    public function create(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER);
    }

    public function update(User $user, Invitation $invitation): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER, $invitation);
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER, $invitation);
    }
}
