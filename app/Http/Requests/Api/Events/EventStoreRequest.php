<?php

namespace App\Http\Requests\Api\Events;

use App\Enums\EventType;
use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class EventStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'person_id' => ['nullable', 'integer', 'exists:people,id'],
            'type' => ['required', Rule::in(EventType::values())],
            'date_exact' => ['nullable', 'date'],
            'date_range' => ['nullable', 'array'],
            'lunar' => ['nullable', 'boolean'],
            'place' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
