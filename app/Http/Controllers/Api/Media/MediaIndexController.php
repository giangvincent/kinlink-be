<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MediaIndexController extends ApiController
{
    public function __invoke(Request $request, string $modelType, int $modelId)
    {
        $class = $this->resolveModelType($modelType);

        /** @var \Illuminate\Database\Eloquent\Model&\Spatie\MediaLibrary\HasMedia $model */
        $model = $class::query()->findOrFail($modelId);

        $this->authorize('view', $model);

        $collection = $request->query('collection');
        $mediaItems = $collection ? $model->getMedia($collection) : $model->media;

        return $this->ok(MediaResource::collection($mediaItems));
    }

    private function resolveModelType(string $type): string
    {
        $aliases = config('kinlink.media.aliases', []);

        if (isset($aliases[$type])) {
            return $aliases[$type];
        }

        $allowed = config('kinlink.media.allowed_models', []);
        if ($allowed && ! in_array($type, $allowed, true)) {
            throw new \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException('Model not allowed for media.');
        }

        return $type;
    }
}
