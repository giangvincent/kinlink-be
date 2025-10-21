<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RelationshipResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'family_id' => $this->resource->family_id,
            'person_id_a' => $this->resource->person_id_a,
            'person_id_b' => $this->resource->person_id_b,
            'type' => $this->resource->type?->value ?? $this->resource->type,
            'certainty' => $this->resource->certainty,
            'source' => $this->resource->source,
            'notes' => $this->resource->notes,
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
