<?php

namespace App\Jobs;

use App\Models\Export;
use App\Models\Family;
use App\Services\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateFamilyBook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly Export $export)
    {
    }

    public function handle(ExportService $exportService): void
    {
        $family = Family::find($this->export->family_id);

        if (! $family) {
            $this->export->status = 'failed';
            $this->export->save();

            return;
        }

        $people = $family->people()->orderBy('surname')->get();

        $payload = $exportService->exportFamilyBook($family, $people);

        $this->export->fill([
            'status' => 'completed',
            'path' => $payload['path'],
            'disk' => $payload['disk'],
            'meta' => ['url' => $payload['url']],
        ])->save();
    }

    public function failed(\Throwable $exception): void
    {
        $this->export->status = 'failed';
        $this->export->meta = array_merge($this->export->meta ?? [], [
            'error' => $exception->getMessage(),
        ]);
        $this->export->save();
    }
}
