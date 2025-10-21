<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Api\ApiController;
use App\Models\Family;
use App\Services\BillingService;
use App\Support\FamilyContext;

class UsageController extends ApiController
{
    public function __construct(private readonly BillingService $billingService)
    {
    }

    public function __invoke(FamilyContext $familyContext)
    {
        $family = $this->resolveFamily($familyContext);
        $this->authorize('view', $family);

        return $this->ok($this->billingService->usage($family));
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
