<?php

namespace App\Http\Requests\Api\Relationships;

use App\Enums\RelationshipType;
use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class RelationshipStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'person_id_a' => ['required', 'integer', 'exists:people,id'],
            'person_id_b' => ['required', 'integer', 'different:person_id_a', 'exists:people,id'],
            'type' => ['required', Rule::in(RelationshipType::values())],
            'certainty' => ['nullable', 'integer', 'between:0,100'],
            'source' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
