<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Media\UploadSignatureRequest;
use App\Services\MediaService;

class UploadSignatureController extends ApiController
{
    public function __construct(private readonly MediaService $mediaService)
    {
    }

    public function __invoke(UploadSignatureRequest $request)
    {
        $signature = $this->mediaService->createUploadSignature(
            $request->input('filename'),
            $request->input('contentType')
        );

        return $this->ok($signature);
    }
}
