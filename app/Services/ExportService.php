<?php

namespace App\Services;

use App\Models\Family;
use App\Models\Person;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class ExportService
{
    public function __construct(private readonly FilesystemManager $filesystemManager)
    {
    }

    /**
     * Render the family book HTML and store as a PDF.
     *
     * @return array{path:string,url:string}
     */
    public function exportFamilyBook(Family $family, Collection $people): array
    {
        $html = view('exports.family-book', [
            'family' => $family,
            'people' => $people,
        ])->render();

        $pdf = Browsershot::html($html)->pdf();

        $diskName = config('kinlink.exports.disk', config('filesystems.default'));
        $disk = $this->filesystemManager->disk($diskName);
        $path = 'exports/'.$family->getKey().'/'.Str::uuid()->toString().'.pdf';

        $disk->put($path, $pdf);

        $url = method_exists($disk, 'url') ? $disk->url($path) : null;

        return [
            'path' => $path,
            'disk' => $diskName,
            'url' => $url,
        ];
    }
}
