<?php

namespace App\Policies;

use App\Enums\FamilyRole;
use App\Models\Relationship;
use App\Models\User;
use App\Policies\Concerns\HandlesFamilyAuthorization;

class RelationshipPolicy
{
    use HandlesFamilyAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::MEMBER);
    }

    public function view(User $user, Relationship $relationship): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::MEMBER, $relationship);
    }

    public function create(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER);
    }

    public function update(User $user, Relationship $relationship): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER, $relationship);
    }

    public function delete(User $user, Relationship $relationship): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER, $relationship);
    }
}
