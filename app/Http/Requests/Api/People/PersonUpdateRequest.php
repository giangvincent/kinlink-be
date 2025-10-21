<?php

namespace App\Http\Requests\Api\People;

use App\Enums\PersonGender;
use App\Enums\PersonVisibility;
use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class PersonUpdateRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'given_name' => ['sometimes', 'string', 'max:255'],
            'middle_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'surname' => ['sometimes', 'string', 'max:255'],
            'display_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'gender' => ['sometimes', 'nullable', Rule::in(PersonGender::values())],
            'birth_date' => ['sometimes', 'nullable', 'date'],
            'death_date' => ['sometimes', 'nullable', 'date'],
            'visibility' => ['sometimes', 'nullable', Rule::in(PersonVisibility::values())],
            'meta' => ['sometimes', 'nullable', 'array'],
        ];
    }
}
