<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Media\MediaAttachRequest;
use App\Http\Resources\MediaResource;
use App\Services\MediaService;

class MediaAttachController extends ApiController
{
    public function __construct(private readonly MediaService $mediaService)
    {
    }

    public function __invoke(MediaAttachRequest $request)
    {
        $modelType = $request->input('model_type');

        $allowed = config('kinlink.media.allowed_models', []);
        if ($allowed && ! in_array($modelType, $allowed, true)) {
            return $this->fail(['message' => ['Model not allowed for media attachments.']], 422);
        }

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $modelType::query()->findOrFail((int) $request->input('model_id'));

        $this->authorize('update', $model);

        $media = $this->mediaService->attachFromUrl(
            $model,
            $model->getKey(),
            $request->input('collection'),
            $request->input('file_url'),
            ['custom_properties' => $request->input('meta', [])]
        );

        return $this->created(new MediaResource($media));
    }
}
