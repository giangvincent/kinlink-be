<?php

namespace App\Http\Resources;

use App\Enums\FamilyRole;
use Illuminate\Http\Request;

class FamilyResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        $role = null;

        if ($this->resource->relationLoaded('members')) {
            $authUser = $request->user();

            if ($authUser) {
                $member = $this->resource->members
                    ->firstWhere('id', $authUser->getKey());

                if ($member && $member->pivot?->role) {
                    $role = $member->pivot->role instanceof FamilyRole
                        ? $member->pivot->role->value
                        : $member->pivot->role;
                }
            }
        } elseif ($this->resource->pivot?->role) {
            $role = $this->resource->pivot->role instanceof FamilyRole
                ? $this->resource->pivot->role->value
                : $this->resource->pivot->role;
        }

        return [
            'id' => $this->resource->getKey(),
            'name' => $this->resource->name,
            'slug' => $this->resource->slug,
            'locale' => $this->resource->locale,
            'billing_plan' => $this->resource->billing_plan?->value ?? $this->resource->billing_plan,
            'settings' => $this->resource->settings,
            'owner_user_id' => $this->resource->owner_user_id,
            'role' => $role,
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
