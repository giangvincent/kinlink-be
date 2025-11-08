<?php

namespace App\Http\Controllers\Api\People\Concerns;

use App\Models\Person;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

trait HandlesPersonMedia
{
    /**
     * @param  array{avatar_file?:UploadedFile|null,avatar_url?:string|null,photos_files?:array<int,UploadedFile>|UploadedFile|null,photo_urls?:array<int,string>|null,clear_avatar?:bool,clear_photos?:bool}  $media
     */
    protected function syncPersonMedia(Person $person, array $media): void
    {
        $avatarFile = $media['avatar_file'] ?? null;
        $avatarUrl = $media['avatar_url'] ?? null;
        $photoFiles = $media['photos_files'] ?? [];
        $photoUrls = $media['photo_urls'] ?? null;
        $clearAvatar = (bool) ($media['clear_avatar'] ?? false);
        $clearPhotos = (bool) ($media['clear_photos'] ?? false);

        if ($avatarFile || $avatarUrl || $clearAvatar) {
            $person->clearMediaCollection('avatar');
        }

        if ($avatarFile instanceof UploadedFile) {
            $this->attachFromFile($person, 'avatar', $avatarFile, 'avatar');
        } elseif ($avatarUrl) {
            $this->attachFromUrl($person, 'avatar', $avatarUrl, 'avatar_url');
        }

        $photoFiles = $photoFiles instanceof UploadedFile ? [$photoFiles] : $photoFiles;
        $hasPhotoFiles = is_array($photoFiles) && count($photoFiles) > 0;
        $hasPhotoUrls = is_array($photoUrls) && count($photoUrls) > 0;

        if ($hasPhotoFiles || $hasPhotoUrls || $clearPhotos) {
            $person->clearMediaCollection('photos');
        }

        if ($hasPhotoFiles) {
            foreach ($photoFiles as $index => $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $this->attachFromFile($person, 'photos', $file, "photos.{$index}");
            }
        } elseif ($hasPhotoUrls) {
            foreach ($photoUrls ?? [] as $index => $url) {
                if (! $url) {
                    continue;
                }

                $this->attachFromUrl($person, 'photos', $url, "photo_urls.{$index}");
            }
        }
    }

    protected function attachFromUrl(Person $person, string $collection, string $url, string $attribute): void
    {
        try {
            $person
                ->addMediaFromUrl($url)
                ->toMediaCollection($collection);
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                $attribute => __('Unable to save the provided media. Please verify the link and try again.'),
            ]);
        }
    }

    protected function attachFromFile(Person $person, string $collection, UploadedFile $file, string $attribute): void
    {
        try {
            $person
                ->addMedia($file)
                ->toMediaCollection($collection);
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                $attribute => __('Unable to upload the provided media. Please try again.'),
            ]);
        }
    }
}
