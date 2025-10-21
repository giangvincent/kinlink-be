<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class InvitationResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'family_id' => $this->resource->family_id,
            'email' => $this->resource->email,
            'role' => $this->resource->role?->value ?? $this->resource->role,
            'token' => $this->resource->token,
            'expires_at' => $this->resource->expires_at?->toIso8601String(),
            'accepted_at' => $this->resource->accepted_at?->toIso8601String(),
            'created_at' => $this->resource->created_at?->toIso8601String(),
        ];
    }
}
