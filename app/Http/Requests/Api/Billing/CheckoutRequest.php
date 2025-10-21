<?php

namespace App\Http\Requests\Api\Billing;

use App\Enums\BillingPlan;
use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'plan' => ['required', Rule::in(BillingPlan::values())],
            'seats' => ['required', 'integer', 'min:1', 'max:500'],
        ];
    }
}
