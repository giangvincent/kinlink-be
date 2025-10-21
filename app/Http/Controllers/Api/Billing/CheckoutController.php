<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Billing\CheckoutRequest;
use App\Models\Family;
use App\Services\BillingService;
use App\Support\FamilyContext;

class CheckoutController extends ApiController
{
    public function __construct(private readonly BillingService $billingService)
    {
    }

    public function __invoke(CheckoutRequest $request, FamilyContext $familyContext)
    {
        $family = $this->resolveFamily($familyContext);

        $this->authorize('update', $family);

        $payload = $this->billingService->checkout(
            $family,
            $request->input('plan'),
            (int) $request->input('seats')
        );

        return $this->ok($payload);
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
