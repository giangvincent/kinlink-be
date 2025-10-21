<?php

namespace App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService
{
    public function __construct(private readonly FilesystemManager $filesystemManager)
    {
    }

    /**
     * Generate a direct upload signature for a remote object storage provider.
     *
     * @param  array{disk?:string,path?:string,expires?:int,visibility?:string}  $options
     * @return array{url:string,headers:array,method:string,expires_at:string,path:string,disk:string}
     */
    public function createUploadSignature(string $filename, string $contentType, array $options = []): array
    {
        $disk = $options['disk'] ?? config('filesystems.cloud', 's3');
        $path = $options['path'] ?? 'uploads/'.Str::uuid()->toString().'/'.$filename;
        $expires = now()->addMinutes($options['expires'] ?? 10);

        /** @var Filesystem $storage */
        $storage = $this->filesystemManager->disk($disk);

        if (! method_exists($storage, 'temporaryUploadUrl')) {
            throw new \RuntimeException("Disk {$disk} does not support presigned uploads.");
        }

        $url = $storage->temporaryUploadUrl($path, $expires, [
            'ContentType' => $contentType,
            'ACL' => $options['visibility'] ?? 'private',
        ]);

        return [
            'url' => $url,
            'headers' => [
                'Content-Type' => $contentType,
            ],
            'method' => 'PUT',
            'expires_at' => $expires->toIso8601String(),
            'path' => $path,
            'disk' => $disk,
        ];
    }

    /**
     * Attach a remote file to a Media Library collection.
     *
     * @param  array{disk?:string,custom_properties?:array}  $options
     */
    public function attachFromUrl(
        string|\Spatie\MediaLibrary\HasMedia $model,
        int $modelId,
        string $collection,
        string $fileUrl,
        array $options = []
    ): Media {
        if (is_string($model)) {
            /** @var class-string<\Spatie\MediaLibrary\HasMedia> $model */
            $modelInstance = $model::query()->findOrFail($modelId);
        } else {
            $modelInstance = $model;
        }

        $disk = $options['disk'] ?? config('filesystems.cloud', 's3');

        $media = $modelInstance
            ->addMediaFromUrl($fileUrl)
            ->withCustomProperties($options['custom_properties'] ?? [])
            ->toMediaCollection($collection, $disk);

        return $media;
    }
}
