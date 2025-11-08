<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaResource extends BaseJsonResource
{
    /** @var Media */
    public $resource;

    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->resource->uuid,
            'id' => $this->resource->getKey(),
            'model_type' => $this->resource->model_type,
            'model_id' => $this->resource->model_id,
            'collection_name' => $this->resource->collection_name,
            'name' => $this->resource->name,
            'file_name' => $this->resource->file_name,
            'mime_type' => $this->resource->mime_type,
            'disk' => $this->resource->disk,
            'size' => $this->resource->size,
            'custom_properties' => $this->resource->custom_properties,
            'generated_conversions' => $this->resource->generated_conversions,
            'original_url' => $this->resource->getFullUrl(),
            'preview_url' => $this->resource->hasGeneratedConversion('thumb')
                ? $this->resource->getFullUrl('thumb')
                : null,
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'updated_at' => $this->resource->updated_at?->toIso8601String(),
        ];
    }
}
