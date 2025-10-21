<?php

namespace App\Policies\Concerns;

use App\Enums\FamilyRole;
use App\Models\Family;
use App\Models\User;
use App\Support\FamilyContext;

trait HandlesFamilyAuthorization
{
    public function __construct(protected FamilyContext $familyContext)
    {
    }

    protected function resolveFamilyId(mixed $model = null): ?int
    {
        if ($model instanceof Family) {
            return $model->getKey();
        }

        if (is_object($model) && isset($model->family_id)) {
            return (int) $model->family_id;
        }

        return $this->familyContext->currentFamilyId();
    }

    protected function userRole(User $user, mixed $model = null): ?FamilyRole
    {
        $familyId = $this->resolveFamilyId($model);

        if ($familyId === null) {
            return null;
        }

        return $user->roleInFamily($familyId);
    }

    protected function hasMinimumRole(User $user, FamilyRole $role, mixed $model = null): bool
    {
        return $this->userRole($user, $model)?->atLeast($role) ?? false;
    }

    protected function isFamilyMember(User $user, mixed $model = null): bool
    {
        return $this->userRole($user, $model) !== null;
    }

    protected function isGuest(User $user, mixed $model = null): bool
    {
        $role = $this->userRole($user, $model);

        return $role === null || $role === FamilyRole::GUEST;
    }
}
