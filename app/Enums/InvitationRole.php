<?php

namespace App\Enums;

use App\Enums\Concerns\HasValues;

enum InvitationRole: string
{
    use HasValues;

    case OWNER = 'owner';
    case ELDER = 'elder';
    case MEMBER = 'member';
    case GUEST = 'guest';

    public function toFamilyRole(): FamilyRole
    {
        return FamilyRole::from($this->value);
    }
}
