<?php

namespace App\Policies;

use App\Enums\FamilyRole;
use App\Models\Export;
use App\Models\User;
use App\Policies\Concerns\HandlesFamilyAuthorization;

class ExportPolicy
{
    use HandlesFamilyAuthorization;

    public function view(User $user, Export $export): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::MEMBER, $export);
    }

    public function create(User $user): bool
    {
        return $this->hasMinimumRole($user, FamilyRole::ELDER);
    }
}
