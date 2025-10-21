<?php

namespace App\Policies;

use App\Enums\FamilyRole;
use App\Models\AuditLog;
use App\Models\User;
use App\Policies\Concerns\HandlesFamilyAuthorization;

class AuditLogPolicy
{
    use HandlesFamilyAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER);
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER, $auditLog);
    }

    public function create(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER);
    }

    public function delete(User $user, AuditLog $auditLog): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::OWNER, $auditLog);
    }
}
