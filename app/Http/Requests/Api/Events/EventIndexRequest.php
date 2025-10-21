<?php

namespace App\Http\Requests\Api\Events;

use App\Http\Requests\Api\ApiRequest;

class EventIndexRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ];
    }
}
