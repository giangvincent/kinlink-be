<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class EventResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'family_id' => $this->resource->family_id,
            'person_id' => $this->resource->person_id,
            'type' => $this->resource->type?->value ?? $this->resource->type,
            'date_exact' => $this->resource->date_exact?->toDateString(),
            'date_range' => $this->resource->date_range,
            'lunar' => (bool) $this->resource->lunar,
            'place' => $this->resource->place,
            'notes' => $this->resource->notes,
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
