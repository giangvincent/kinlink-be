<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Api\ApiController;
use App\Services\BillingService;
use Illuminate\Http\Request;

class StripeWebhookController extends ApiController
{
    public function __construct(private readonly BillingService $billingService)
    {
    }

    public function __invoke(Request $request)
    {
        $this->billingService->handleStripeWebhook($request->all());

        return $this->noContent();
    }
}
