<?php

namespace App\Http\Controllers\Api\Exports;

use App\Http\Controllers\Api\ApiController;
use App\Jobs\GenerateFamilyBook;
use App\Models\Export;
use App\Models\Family;
use App\Support\FamilyContext;

class FamilyBookExportController extends ApiController
{
    public function __invoke(FamilyContext $familyContext)
    {
        $family = $this->resolveFamily($familyContext);

        $this->authorize('create', Export::class);
        $this->authorize('view', $family);

        $export = Export::create([
            'family_id' => $family->getKey(),
            'type' => 'family-book',
            'status' => 'queued',
        ]);

        GenerateFamilyBook::dispatch($export);

        return $this->created([
            'job_id' => $export->getKey(),
            'status' => $export->status,
        ]);
    }

    private function resolveFamily(FamilyContext $familyContext): Family
    {
        $familyId = $familyContext->currentFamilyId();

        if (! $familyId) {
            abort(400, 'Family context not set.');
        }

        return Family::findOrFail($familyId);
    }
}
