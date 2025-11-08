<?php

namespace App\Http\Resources;

use App\Enums\RelationshipType;
use Illuminate\Http\Request;

class PersonResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        $relationships = [];
        $avatarMedia = $this->resource->getFirstMedia('avatar');
        $photoMedia = $this->resource->getMedia('photos');

        if ($this->resource->relationLoaded('primaryRelationships')) {
            $relationships = array_merge(
                $relationships,
                RelationshipResource::collection($this->resource->primaryRelationships)->toArray($request)
            );
        }

        if ($this->resource->relationLoaded('relatedRelationships')) {
            $relationships = array_merge(
                $relationships,
                RelationshipResource::collection($this->resource->relatedRelationships)->toArray($request)
            );
        }

        return [
            'id' => $this->resource->getKey(),
            'family_id' => $this->resource->family_id,
            'given_name' => $this->resource->given_name,
            'middle_name' => $this->resource->middle_name,
            'surname' => $this->resource->surname,
            'display_name' => $this->resource->display_name,
            'gender' => $this->resource->gender?->value ?? $this->resource->gender,
            'birth_date' => $this->resource->birth_date?->toDateString(),
            'death_date' => $this->resource->death_date?->toDateString(),
            'is_deceased' => (bool) $this->resource->is_deceased,
            'visibility' => $this->resource->visibility?->value ?? $this->resource->visibility,
            'meta' => $this->resource->meta,
            'closest_relationship' => $this->resource->closest_relationship,
            'closest_relative_id' => $this->resource->closest_relative_id,
            'closest_relative' => $this->whenLoaded('closestRelative', function () {
                return [
                    'id' => $this->resource->closestRelative?->getKey(),
                    'display_name' => $this->resource->closestRelative?->display_name,
                ];
            }),
            'avatar' => $avatarMedia ? MediaResource::make($avatarMedia) : null,
            'photos' => $photoMedia->isNotEmpty() ? MediaResource::collection($photoMedia) : [],
            'approval_suggestion' => $this->resource->closestRelative
                ? __('Confirm changes with :name for accuracy.', ['name' => $this->resource->closestRelative->display_name])
                : null,
            'relationships' => array_values($relationships),
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
