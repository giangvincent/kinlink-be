<?php

namespace App\Http\Resources;

use App\Enums\FamilyRole;
use Illuminate\Http\Request;

class MembershipResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        $role = $this->resource->pivot->role ?? null;

        if (is_string($role) && enum_exists(FamilyRole::class)) {
            $role = FamilyRole::from($role);
        }

        return [
            'family_id' => $this->resource->getKey(),
            'family_name' => $this->resource->name,
            'family_slug' => $this->resource->slug,
            'role' => $role instanceof FamilyRole ? $role->value : $role,
        ];
    }
}
