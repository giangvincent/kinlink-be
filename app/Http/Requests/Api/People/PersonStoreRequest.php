<?php

namespace App\Http\Requests\Api\People;

use App\Enums\PersonGender;
use App\Enums\PersonVisibility;
use App\Http\Requests\Api\ApiRequest;
use Illuminate\Validation\Rule;

class PersonStoreRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'given_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(PersonGender::values())],
            'birth_date' => ['nullable', 'date'],
            'death_date' => ['nullable', 'date', 'after_or_equal:birth_date'],
            'visibility' => ['nullable', Rule::in(PersonVisibility::values())],
            'meta' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('display_name') && $this->filled('given_name')) {
            $this->merge([
                'display_name' => trim(
                    collect([$this->input('given_name'), $this->input('middle_name'), $this->input('surname')])
                        ->filter()
                        ->implode(' ')
                ),
            ]);
        }
    }
}
