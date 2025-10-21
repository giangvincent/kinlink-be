<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class UserResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'locale' => $this->resource->locale,
            'time_zone' => $this->resource->time_zone,
            'email_verified_at' => $this->resource->email_verified_at?->toIso8601String(),
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
