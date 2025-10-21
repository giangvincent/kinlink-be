<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PostResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'family_id' => $this->resource->family_id,
            'author_user_id' => $this->resource->author_user_id,
            'body' => $this->resource->body,
            'visibility' => $this->resource->visibility?->value ?? $this->resource->visibility,
            'pinned' => (bool) $this->resource->pinned,
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
