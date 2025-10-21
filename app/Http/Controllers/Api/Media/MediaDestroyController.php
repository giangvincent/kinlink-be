<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Api\ApiController;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaDestroyController extends ApiController
{
    public function __invoke(string $uuid)
    {
        $media = Media::query()->where('uuid', $uuid)->firstOrFail();

        $model = $media->model;
        if ($model) {
            $this->authorize('update', $model);
        }

        $media->delete();

        return $this->noContent();
    }
}
