<?php

namespace App\Policies;

use App\Enums\FamilyRole;
use App\Enums\PersonVisibility;
use App\Models\Person;
use App\Models\User;
use App\Policies\Concerns\HandlesFamilyAuthorization;

class PersonPolicy
{
    use HandlesFamilyAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->isFamilyMember($user);
    }

    public function view(User $user, Person $person): bool
    {
        $role = $this->userRole($user, $person);

        if ($role === null) {
            return false;
        }

        return match ($person->visibility) {
            PersonVisibility::PRIVATE => $role->atLeast(FamilyRole::ELDER),
            PersonVisibility::FAMILY => $role->atLeast(FamilyRole::MEMBER),
            PersonVisibility::GUESTS => $role->atLeast(FamilyRole::GUEST),
        };
    }

    public function create(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER);
    }

    public function update(User $user, Person $person): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER, $person);
    }

    public function delete(User $user, Person $person): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER, $person);
    }
}
