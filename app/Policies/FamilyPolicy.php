<?php

namespace App\Policies;

use App\Enums\FamilyRole;
use App\Models\Family;
use App\Models\User;
use App\Policies\Concerns\HandlesFamilyAuthorization;

class FamilyPolicy
{
    use HandlesFamilyAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Family $family): bool
    {
        return $this->isFamilyMember($user, $family);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Family $family): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER, $family);
    }

    public function delete(User $user, Family $family): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER, $family);
    }
}
