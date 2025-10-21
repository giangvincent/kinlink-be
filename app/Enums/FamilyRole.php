<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum FamilyRole: string
{
    use HasValues;

    case OWNER = 'owner';
    case ELDER = 'elder';
    case MEMBER = 'member';
    case GUEST = 'guest';

    public function rank(): int
    {
        return match ($this) {
            self::GUEST => 1,
            self::MEMBER => 2,
            self::ELDER => 3,
            self::OWNER => 4,
        };
    }

    public function atLeast(self $role): bool
    {
        return $this->rank() >= $role->rank();
    }
}
